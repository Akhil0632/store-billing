<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;
use Exception;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                ['name'  => $data['customer_name']]
            );

            $subtotal = 0;
            $taxTotal = 0;
            $orderItemsData = [];

            $items = collect($data['items'])
                ->sortBy('product_id')
                ->values()
                ->all();

            foreach ($items as $item) {
                // ─────────────────────────────────────────────────────
                // LOCK the row with SELECT ... FOR UPDATE.
                // Any other transaction trying to read this same row
                // with lockForUpdate() will BLOCK until we commit/rollback.
                // ─────────────────────────────────────────────────────
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new Exception(
                        "Insufficient stock for '{$product->name}'. Available: {$product->stock}, Requested: {$item['quantity']}."
                    );
                }

                // ─────────────────────────────────────────────────────
                // ATOMIC conditional decrement. The WHERE clause makes
                // the check + update a single atomic DB operation.
                // If another transaction snuck in and reduced stock
                // between our lockForUpdate and here (shouldn't happen,
                // but belt-and-suspenders), this returns 0 and we throw.
                // ─────────────────────────────────────────────────────
                $affected = Product::query()
                    ->whereKey($product->id)
                    ->where('stock', '>=', $item['quantity'])
                    ->decrement('stock', $item['quantity']);

                if ($affected === 0) {
                    throw new Exception(
                        "Failed to reserve stock for '{$product->name}'. Please try again."
                    );
                }

                $line_subtotal = $product->price * $item['quantity'];
                $lineTax   = $line_subtotal * ($product->tax_percentage / 100);

                $subtotal += $line_subtotal;
                $taxTotal += $lineTax;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price'      => $product->price,
                    'line_total' => $line_subtotal + $lineTax,
                ];
            }

            $grandTotal = $subtotal + $taxTotal;
            $change     = $data['amount_given'] - $grandTotal;

            if ($change < 0) {
                throw new \RuntimeException(
                    "Amount given (€" . number_format($data['amount_given'], 2) .
                    ") is less than the grand total (€" . number_format($grandTotal, 2) . ")."
                );
            }

            $order = Order::create([
                'customer_id'     => $customer->id,
                'subtotal'        => $subtotal,
                'tax_total'       => $taxTotal,
                'grand_total'     => $grandTotal,
                'amount_given'    => $data['amount_given'],
                'change_returned' => $change,
            ]);

            $order->items()->createMany($orderItemsData);

            return $order->load('customer', 'items.product');
        });

        SendOrderConfirmationEmail::dispatch($order);

        return $order;
    }

    public function getOrderHistoryByEmail(string $email, int $perPage = 15, ?string $from = null, ?string $to = null): LengthAwarePaginator
    {
        $customer = Customer::where('email', $email)->first();

        if (! $customer) {
            throw (new ModelNotFoundException())->setModel(Customer::class);
        }

        return $customer->orders()
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate($perPage);
    }
}

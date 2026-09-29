<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'subtotal', 'tax_total', 'grand_total', 'amount_given', 'change_returned',];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_total'       => 'decimal:2',
        'grand_total'     => 'decimal:2',
        'amount_given'    => 'decimal:2',
        'change_returned' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Create an order with line items and decrement product stock atomically.
     *
     * @param  array{customer_id:int, items:array<int, array{product_id:int, quantity:int}>}  $data
     */
    public static function createWithItems(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $order = self::create([
                'customer_id' => $data['customer_id'],
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
            ]);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($data['items'] as $line) {
                // Lock the product row to prevent concurrent stock race conditions
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);
                $quantity = (int) $line['quantity'];

                if (! $product->hasStock($quantity)) {
                    throw new \RuntimeException(
                        "Insufficient stock for {$product->name} ({$product->code}). " .
                        "Available: {$product->stock}, requested: {$quantity}."
                    );
                }

                $lineSubtotal = round($product->price * $quantity, 2);
                $lineTax = round($lineSubtotal * ($product->tax_percentage / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $quantity);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
            }

            $order->update([
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'grand_total' => round($subtotal + $taxTotal, 2),
            ]);

            return $order->load('items.product', 'customer');
        });
    }
}

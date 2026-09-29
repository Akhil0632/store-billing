<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('customer', 'items')
            ->latest()
            ->paginate(15);

        $lowStockProducts = Product::where('stock', '<', config('inventory.low_stock_threshold', 5))
            ->orderBy('stock')
            ->get();

        return view('orders.index', compact('orders', 'lowStockProducts'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $lowStockProducts = Product::where('stock', '<', config('inventory.low_stock_threshold', 5))
            ->orderBy('stock')
            ->get();

        return view('orders.create', [
            'customers'        => Customer::orderBy('name')->get(),
            'products'         => $products,
            'productsJson'     => $products->map(fn ($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'price'          => (float) $p->price,
                'tax_percentage' => (float) $p->tax_percentage,
                'stock'          => $p->stock,
            ])->values()->all(),
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_email'     => ['required', 'email'],
            'customer_name'      => ['required', 'string', 'max:255'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'amount_given'       => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::firstOrCreate(
                ['email' => $validated['customer_email']],
                ['name'  => $validated['customer_name']]
            );

            $subtotal       = 0;
            $taxTotal       = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Atomic stock check-and-deduct
                $affected = DB::table('products')
                    ->where('id', $product->id)
                    ->where('stock', '>=', $item['quantity'])
                    ->update(['stock' => DB::raw("stock - {$item['quantity']}")]);

                if ($affected === 0) {
                    throw new \RuntimeException("Not enough stock for {$product->name}.");
                }

                $lineSubtotal = $product->price * $item['quantity'];
                $lineTax      = $lineSubtotal * ($product->tax_percentage / 100);
                $lineTotal    = $lineSubtotal;

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                $orderItemsData[] = [
                    'product_id'     => $product->id,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'line_subtotal'  => $lineSubtotal,
                    'line_tax'       => $lineTax,
                    'line_total'     => $lineTotal,
                ];
            }

            $grandTotal     = $subtotal + $taxTotal;
            $changeReturned = $validated['amount_given'] - $grandTotal;

            if ($changeReturned < 0) {
                throw new \RuntimeException(
                    "Amount given (€" . number_format($validated['amount_given'], 2) .
                    ") is less than the grand total (€" . number_format($grandTotal, 2) . ")."
                );
            }

            $order = Order::create([
                'customer_id'     => $customer->id,
                'subtotal'        => $subtotal,
                'tax_total'       => $taxTotal,
                'grand_total'     => $grandTotal,
                'amount_given'    => $validated['amount_given'],
                'change_returned' => $changeReturned,
            ]);

            $order->items()->createMany($orderItemsData);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Order #' . $order->id . ' generated successfully!',
                    'order_id' => $order->id,
                    'change'   => number_format($changeReturned, 2),
                ]);
            }

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order #' . $order->id . ' created successfully.');

        } catch (\RuntimeException $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }
    }


    public function show(Order $order)
    {
        $order->load('customer', 'items.product');

        return view('orders.show', compact('order'));
    }
}

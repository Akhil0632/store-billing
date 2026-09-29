<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\CustomerOrderHistoryRequest;
use App\Http\Resources\OrderHistoryResource;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return OrderResource::make($order)
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $customer = Customer::where('email', $request->query('email'))->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $orders = $customer->orders()
            ->with('items.product')
            ->when($request->filled('from'), fn ($q) =>
                $q->where('created_at', '>=', $request->date('from'))
            )
            ->when($request->filled('to'), fn ($q) =>
                $q->where('created_at', '<=', $request->date('to')->endOfDay())
            )
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return OrderResource::collection($orders)->additional([
            'success' => true,
        ]);
    }

    public function show(Order $order): OrderResource
    {
        $order->load('customer', 'items.product');

        return OrderResource::make($order);
    }

}

@extends('layouts.app')

@section('title', 'Order #' . $order->id)
@section('header-title', 'Store Billing — Order #' . $order->id)
@section('header-subtitle', 'ORDER RECEIPT')

@section('content')
    <div class="container">

        <!-- LEFT COLUMN -->
        <div class="left-column">

            <a href="{{ route('orders.index') }}" class="btn-link" style="display:inline-block; margin-bottom:15px;">
                ← All Orders
            </a>

            <div class="section-title">Order #{{ $order->id }}</div>

            <p style="font-size:0.9rem; margin-bottom:20px;">
                <strong>Customer:</strong> {{ $order->customer->name }}<br>
                <span class="text-muted">{{ $order->customer->email }}</span><br>
                <span class="text-muted">Placed: {{ $order->created_at->format('Y-m-d H:i') }}</span>
            </p>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right" style="width: 90px;">Unit Price</th>
                        <th class="text-right" style="width: 50px;">Qty</th>
                        <th class="text-right" style="width: 90px;">Subtotal</th>
                        <th class="text-right" style="width: 110px;">Tax</th>
                        <th class="text-right" style="width: 100px;">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name }}<br>
                                <span class="text-muted" style="font-size:0.75rem;">{{ $item->product->code }}</span>
                            </td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->line_subtotal, 2) }}</td>
                            <td class="text-right">
                                {{ number_format($item->line_tax, 2) }}
                                <span class="text-muted" style="font-size:0.75rem;">({{ $item->tax_percentage }}%)</span>
                            </td>
                            <td class="text-right"><strong>{{ number_format($item->line_total, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">

            <div class="payment-box">
                <div class="section-title" style="margin-top:0;">Payment Summary</div>

                <div class="payment-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="payment-row">
                    <span>Tax</span>
                    <span>{{ number_format($order->tax_total, 2) }}</span>
                </div>
                <div class="payment-row total">
                    <span>Grand Total</span>
                    <span>{{ number_format($order->grand_total, 2) }}</span>
                </div>

                @if(!is_null($order->amount_given))
                    <div class="amount-given-input">
                        <div class="payment-row">
                            <span>Amount Given</span>
                            <span>{{ number_format($order->amount_given, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span>Change Returned</span>
                            <span>{{ number_format($order->change_returned, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-primary" style="width:100%; text-align:center;">
                Back to Orders
            </a>

        </div>
    </div>
@endsection
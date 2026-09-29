@extends('layouts.app')

@section('title', 'Orders')
@section('header-title', 'Store Billing — Orders')
@section('header-subtitle', 'ORDER HISTORY')

@section('content')
    <div class="container">

        <!-- LEFT COLUMN -->
        <div class="left-column">

            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <div class="section-title">All Orders</div>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Customer</th>
                        <th style="width: 70px;">Items</th>
                        <th style="width: 100px;" class="text-right">Subtotal</th>
                        <th style="width: 90px;" class="text-right">Tax</th>
                        <th style="width: 110px;" class="text-right">Grand Total</th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                {{ $order->customer->name }}<br>
                                <span class="text-muted" style="font-size:0.75rem;">{{ $order->customer->email }}</span>
                            </td>
                            <td>{{ $order->items->count() }}</td>
                            <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                            <td class="text-right">{{ number_format($order->tax_total, 2) }}</td>
                            <td class="text-right"><strong>{{ number_format($order->grand_total, 2) }}</strong></td>
                            <td><a href="{{ route('orders.show', $order) }}" class="btn-link">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted" style="text-align:center; padding: 30px;">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {{ $orders->links() }}
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">

            <a href="{{ route('orders.create') }}" class="btn btn-primary" style="width:100%; text-align:center;">
                + New Order
            </a>

        </div>
    </div>
@endsection
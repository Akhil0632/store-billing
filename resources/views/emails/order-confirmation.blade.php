@component('mail::message')
# Thanks for your order, {{ $customer->name }}!

Your order **#{{ $order->id }}** has been confirmed.

@component('mail::table')
| Product | Qty | Unit Price | Line Total |
|:--------|:---:|-----------:|-----------:|
@foreach ($items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | €{{ number_format($item->price, 2) }} | €{{ number_format($item->line_total, 2) }} |
@endforeach
@endcomponent

**Subtotal:** €{{ number_format($order->subtotal, 2) }}
**Tax:** €{{ number_format($order->tax_total, 2) }}
**Grand Total:** €{{ number_format($order->grand_total, 2) }}
**Amount Given:** €{{ number_format($order->amount_given, 2) }}
**Change Returned:** €{{ number_format($order->change_returned, 2) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent

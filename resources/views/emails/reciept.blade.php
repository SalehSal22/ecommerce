<x-mail::message>
<div style="background: #111827; padding: 2rem; text-align: center; margin: -2rem -2rem 2rem; border-radius: 8px 8px 0 0;">
    <p style="font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #9ca3af; margin: 0 0 8px;">Order Confirmed</p>
    <p style="font-size: 26px; font-weight: 600; color: #ffffff; margin: 0;">Thank you!</p>
</div>

Hello {{ $order->user->name ?? 'there' }},

Your order has been placed successfully. Here's a summary of what you ordered.

<x-mail::panel>
<table style="width: 100%; font-size: 13px; border-collapse: collapse;">
    <tr>
        <td style="color: #6b7280; padding: 5px 0;">Order ID</td>
        <td style="text-align: right; font-weight: 600; color: #111827; padding: 5px 0;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
    </tr>
    <tr>
        <td style="color: #6b7280; padding: 5px 0;">Status</td>
        <td style="text-align: right; padding: 5px 0;">
            <span style="background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                {{ $order->status }}
            </span>
        </td>
    </tr>
    <tr>
        <td style="color: #6b7280; padding: 5px 0;">Date</td>
        <td style="text-align: right; font-weight: 600; color: #111827; padding: 5px 0;">{{ $order->created_at->format('M d, Y') }}</td>
    </tr>
</table>
</x-mail::panel>

## Order Items

<x-mail::table>
| Product | Qty | Price | Subtotal |
|:--------|----:|------:|---------:|
@foreach ($order->items as $item)
| **{{ $item->product->name }}** | {{ $item->quantity }} | ${{ number_format((float) $item->price, 2) }} | ${{ number_format((float) $item->subtotal, 2) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
<table style="width: 100%;">
    <tr>
        <td style="font-size: 15px; font-weight: 600; color: #111827;">Total</td>
        <td style="text-align: right; font-size: 22px; font-weight: 700; color: #111827;">${{ number_format((float) $order->total, 2) }}</td>
    </tr>
</table>
</x-mail::panel>

Have questions about your order? Just reply to this email and we'll get back to you.

Thanks,
**{{ config('app.name') }}**

<x-mail::subcopy>
This email was sent to you because you placed an order on {{ config('app.name') }}. Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}.
</x-mail::subcopy>
</x-mail::message>
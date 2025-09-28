@component('mail::message')
# Hello {{ $order->user->name ?? $order->contact_name ?? 'Guest' }},


Thank you for your order! Here's your receipt:

---

### 🧾 **Order Summary**
**Order ID:** #{{ $order->id }}  
**Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}

---

### 📦 **Items Ordered**
@component('mail::table')
| Product        | Quantity | Unit Price (AED) | Subtotal (AED) |
|----------------|:--------:|:----------------:|---------------:|
@foreach ($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->price_per_unit, 2) }} | {{ number_format($item->subtotal, 2) }} |
@endforeach
@endcomponent

---

### 🚚 **Shipping Details**
@if ($order->shippingAddress)
**Phone:** {{ $order->shippingAddress->phone }}  
**Address:**  
{{ $order->shippingAddress->address_line }},  
{{ $order->shippingAddress->area->name ?? '-' }},  
{{ $order->shippingAddress->city->name ?? '-' }}
@endif

---

### 💳 **Payment & Charges**
- **Payment Method:** {{ ucfirst($order->payment_method) }}
- **Subtotal:** AED {{ number_format($order->subtotal, 2) }}
- **Shipping:** AED {{ number_format($order->shipping_cost, 2) }}
- **Total Paid:** **AED {{ number_format($order->total, 2) }}**

---

### 📦 **Shipping Cost Breakdown**
@if (is_array($order->shipping_breakdown))
@component('mail::table')
| Component        | Amount (AED) |
|------------------|--------------:|
| Base Cost        | {{ number_format($order->shipping_breakdown['base_cost'] ?? 0, 2) }} |
| Weight Charges   | {{ number_format($order->shipping_breakdown['weight_charges'] ?? 0, 2) }} |
| Fuel Surcharge   | {{ number_format($order->shipping_breakdown['fuel_surcharge'] ?? 0, 2) }} |
| Packaging        | {{ number_format($order->shipping_breakdown['packaging'] ?? 0, 2) }} |
| AED              | {{ number_format($order->shipping_breakdown['epg'] ?? 0, 2) }} |
| VAT              | {{ number_format($order->shipping_breakdown['vat'] ?? 0, 2) }} |
@endcomponent
@endif

---

If you have any questions, feel free to [contact us](mailto:support@{{ request()->getHost() }}).

Thanks again for shopping with us!  
**{{ config('app.name') }} Team**
@endcomponent

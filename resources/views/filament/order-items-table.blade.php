@php
    /** @var \App\Models\Order $order */
    $order = $getRecord();
@endphp

<div class="mb-4 text-sm">
    <div><strong>Customer:</strong> {{ $order->user?->name ?? $order->contact_name ?? '-' }}</div>
    <div><strong>Email:</strong> {{ $order->user?->email ?? $order->contact_email ?? '-' }}</div>
    <div><strong>Phone:</strong> {{ $order->user?->phone ?? $order->contact_phone ?? '-' }}</div>
    <div><strong>Status:</strong>
    @php
        $status = $order->status ?? 'pending';
        $statusColor = match ($status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed', 'cancelled', 'payment_failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
    @endphp

    <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
        {{ $statusLabel }}
    </span>
</div>

<table class="w-full text-sm">
    <thead>
        <tr>
            <th class="text-left p-2">Type</th>
            <th class="text-left p-2">Product</th>
            <th class="text-left p-2">Price</th>
            <th class="text-left p-2">Qty</th>
            <th class="text-left p-2">Shipping Option</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
            @php
                $product = $item->buyable;
                $type = class_basename($item->buyable_type);
                $price = $item->price_per_unit;

                $shippingOption = $item->shipping_option ?? 'delivery_only';
                $shippingLabel = match ($shippingOption) {
                    'delivery_only' => 'Delivery Only',
                    'with_installation' => 'Delivery + Home Installation',
                    'installation_center' => 'Installation Center (Pick-up)',
                    default => 'Unknown',
                };

                $extraDetails = '';
                if ($shippingOption === 'with_installation') {
                    $extraDetails = $item->mobileVan?->name
                        ? "Mobile Van: {$item->mobileVan->name} on " . \Carbon\Carbon::parse($item->installation_date)->format('d M Y')
                        : "Mobile Van: Not Assigned";
                } elseif ($shippingOption === 'installation_center') {
                    $extraDetails = $item->installationCenter?->name
                        ? "Installer Shop: {$item->installationCenter->name} on " . \Carbon\Carbon::parse($item->installation_date)->format('d M Y')
                        : "Installer Shop: Not Assigned";
                }
            @endphp

            <tr class="border-t">
                <td class="p-2">{{ $type }}</td>
                <td class="p-2">{{ $product->name ?? '-' }}</td>
                <td class="p-2">{{ number_format($price, 2) }} AED</td>
                <td class="p-2">{{ $item->quantity }}</td>
                <td class="p-2">
                    <div class="font-medium">{{ $shippingLabel }}</div>
                    @if ($extraDetails)
                        <div class="text-gray-600 text-xs">{{ $extraDetails }}</div>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

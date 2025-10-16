<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt #{{ $order->id }} – O2Mart</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background-color: #df2020;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
        }
        .header img {
            width: 95px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 3px;
        }
        .header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 25px;
        }
        .header a {
            color: #ffffff;
            text-decoration: none;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .content {
            padding: 30px 20px;
        }
        .order-box {
            background-color: #f8f9fa;
            border: 2px solid #df2020;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .order-number {
            font-size: 28px;
            font-weight: bold;
            color: #df2020;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            margin-bottom: 10px;
        }
        .detail-row strong {
            color: #df2020;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th,
        .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #df2020;
            color: white;
            font-weight: bold;
        }
        .items-table td:last-child,
        .items-table th:last-child {
            text-align: right;
        }
        .whatsapp-link {
            background-color: #df2020;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        .footer {
            background-color: #df2020;
            color: #ffffff;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }
        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <a href="https://www.o2mart.net" target="_blank" style="display: inline-block">
            <img src="https://i.ibb.co/0VXXGcfy/logo.png" alt="logo" border="0" />
        </a>
        <h2>– Order Receipt</h2>
    </div>

    <div class="content">
        <p>Hello {{ $order->user->name ?? $order->contact_name ?? 'Guest' }},</p>
        
        <p>Thank you for your order! Here's your receipt:</p>

        <div class="order-box">
            <div>Order Number:</div>
            <div class="order-number">#{{ $order->id }}</div>
            <div>{{ $order->created_at->format('d M Y, h:i A') }}</div>
        </div>

        <div class="details">
            <div class="detail-row">
                <strong>Items Ordered:</strong>
            </div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price_per_unit, 2) }} AED</td>
                        <td>{{ number_format($item->subtotal, 2) }} AED</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($order->shippingAddress)
            <div class="detail-row">
                <strong>Shipping Address:</strong><br>
                {{ $order->shippingAddress->address_line }}<br>
                {{ $order->shippingAddress->area->name ?? '-' }}, {{ $order->shippingAddress->city->name ?? '-' }}<br>
                Phone: {{ $order->shippingAddress->phone }}
            </div>
            @endif

            <div class="detail-row">
                <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
            </div>

            @if ($order->tracking_number)
            <div class="detail-row">
                <strong>Tracking Number:</strong> {{ $order->tracking_number }}
            </div>
            @endif

            <div class="detail-row">
                <strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }} AED
            </div>

            <div class="detail-row">
                <strong>Shipping:</strong> {{ number_format($order->shipping_cost, 2) }} AED
            </div>

            <div class="detail-row">
                <strong>Total Paid:</strong> {{ number_format($order->total, 2) }} AED
            </div>
        </div>

        <p>We'll process your order and get back to you shortly with tracking information.</p>
        <a href="https://www.o2mart.net" class="whatsapp-link">Visit Our Store</a>

        <p>We appreciate your trust in O2Mart – making car care easy, reliable, and hassle-free in the UAE.</p>
        
        <p>Best regards,<br>
        The O2Mart Team<br>
        <a href="www.o2mart.net">www.o2mart.net</a> | +971 56 178 7270</p>
    </div>

    <div class="footer" style="text-align: center; padding: 10px 0; background-color: #df2020">
        <p style="font-weight: bold">Follow us:</p>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 10px">
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/company/o2mart/" target="_blank" style="width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <img src="https://i.ibb.co/bjRrfjKJ/icons8-linkedin-24.png" alt="LinkedIn" style="width: 16px; height: 16px; display: block" />
            </a>
            
            <!-- Facebook -->
            <a href="https://www.facebook.com/o2mart" target="_blank" style="width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <img src="https://i.ibb.co/nNcJpzQw/icons8-facebook-logo-50.png" alt="Facebook" style="width: 16px; height: 16px; display: block" />
            </a>
            
            <!-- Instagram -->
            <a href="https://www.instagram.com/o2mart/" target="_blank" style="width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <img src="https://i.ibb.co/F4xXdtXp/icons8-instagram-logo-50-1.png" alt="Instagram" style="width: 16px; height: 16px; display: block" />
            </a>
            
            <!-- WhatsApp -->
            <a href="https://wa.me/971561787270" target="_blank" style="width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <img src="https://i.ibb.co/20ZJMSmx/icons8-whatsapp-50.png" alt="WhatsApp" style="width: 16px; height: 16px; display: block" />
            </a>
        </div>
        
        <p style="margin-top: 6px; font-size: 12px">&copy; {{ date('Y') }} O2Mart. All rights reserved.</p>
    </div>
</div>
</body>
</html>
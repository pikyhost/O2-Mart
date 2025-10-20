<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Order Receipt #{{ $order->id }} – O2Mart</title>
  </head>
  <body style="margin:0; padding:0; background-color:#F5F5F5;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F5F5F5">
      <tr>
        <td align="center">
          <!-- Main Container -->
          <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" style="width:600px; max-width:600px; margin:0 auto;">
            <!-- Header -->
            <tr>
              <td bgcolor="#DF2020" style="padding:15px 20px;" align="left">
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td valign="middle" align="left">
                      <a href="https://www.o2mart.net" target="_blank" style="display:inline-block;">
                        <img src="https://o2mart.to7fa.online/email-assets/logo.png?v=5" alt="O2Mart Logo" width="170" height="50" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td valign="middle" align="left" style="color:#ffffff; font-size:20px; font-family:Arial, sans-serif; padding-left:10px;">
                      – Order Receipt
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td style="padding:30px 25px; font-family:Arial, sans-serif; color:#333333; font-size:15px; line-height:1.6;">
                <p style="margin:0 0 15px 0;">Hello {{ $order->user->name ?? $order->contact_name ?? 'Guest' }},</p>
                <p style="margin:0 0 20px 0;">Thank you for your order\! Here's your receipt:</p>

                <!-- Order Box -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color:#f8f9fa; border:2px solid #df2020; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td align="center">
                      <p style="margin:0 0 10px 0; font-size:14px;">Order Number:</p>
                      <p style="margin:10px 0; font-size:28px; font-weight:bold; color:#df2020; letter-spacing:2px;">#{{ $order->id }}</p>
                      <p style="margin:10px 0 0 0; font-size:14px;">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </td>
                  </tr>
                </table>

                <!-- Details -->
                <table width="100%" cellpadding="15" cellspacing="0" border="0" style="background-color:#f8f9fa; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td>
                      <p style="margin:0 0 15px 0;"><strong style="color:#df2020;">Items Ordered:</strong></p>
                      
                      <!-- Items Table -->
                      <table width="100%" cellpadding="10" cellspacing="0" border="0" style="margin:15px 0;">
                        <thead>
                          <tr style="background-color:#df2020; color:#ffffff;">
                            <th style="padding:10px; text-align:left; font-family:Arial, sans-serif; font-size:13px;">Product</th>
                            <th style="padding:10px; text-align:center; font-family:Arial, sans-serif; font-size:13px;">Qty</th>
                            <th style="padding:10px; text-align:right; font-family:Arial, sans-serif; font-size:13px;">Unit Price</th>
                            <th style="padding:10px; text-align:right; font-family:Arial, sans-serif; font-size:13px;">Subtotal</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($order->items as $item)
                          <tr style="border-bottom:1px solid #ddd;">
                            <td style="padding:10px; text-align:left; font-family:Arial, sans-serif; font-size:13px;">{{ $item->product_name }}</td>
                            <td style="padding:10px; text-align:center; font-family:Arial, sans-serif; font-size:13px;">{{ $item->quantity }}</td>
                            <td style="padding:10px; text-align:right; font-family:Arial, sans-serif; font-size:13px;">{{ number_format($item->price_per_unit, 2) }} AED</td>
                            <td style="padding:10px; text-align:right; font-family:Arial, sans-serif; font-size:13px;">{{ number_format($item->subtotal, 2) }} AED</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>

                      @if ($order->shippingAddress)
                      <p style="margin:15px 0 10px 0;"><strong style="color:#df2020;">Shipping Address:</strong><br/>
                        {{ $order->shippingAddress->address_line }}<br/>
                        {{ $order->shippingAddress->area->name ?? '-' }}, {{ $order->shippingAddress->city->name ?? '-' }}<br/>
                        Phone: {{ $order->shippingAddress->phone }}
                      </p>
                      @endif

                      <p style="margin:10px 0;"><strong style="color:#df2020;">Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>

                      @if ($order->tracking_number)
                      <p style="margin:10px 0;"><strong style="color:#df2020;">Tracking Number:</strong> {{ $order->tracking_number }}</p>
                      @endif

                      @php
                        $installationCenterItems = $order->items->where('shipping_option', 'installation_center');
                      @endphp
                      
                      @if ($installationCenterItems->count() > 0)
                      <p style="margin:15px 0 10px 0;"><strong style="color:#df2020;">Center Shipping:</strong></p>
                      @foreach ($installationCenterItems as $item)
                        @if ($item->installationCenter)
                        <p style="margin:10px 0;"><strong style="color:#df2020;">Name:</strong> {{ $item->installationCenter->name }}</p>
                        <p style="margin:10px 0;"><strong style="color:#df2020;">Location:</strong> {{ $item->installationCenter->location }}</p>
                        @if ($item->installation_date)
                        <p style="margin:10px 0;"><strong style="color:#df2020;">Scheduled Date:</strong> {{ \Carbon\Carbon::parse($item->installation_date)->format('d M Y, h:i A') }}</p>
                        @endif
                        @endif
                      @endforeach
                      @endif

                      <p style="margin:10px 0;"><strong style="color:#df2020;">Subtotal:</strong> {{ number_format($order->subtotal, 2) }} AED</p>
                      <p style="margin:10px 0;"><strong style="color:#df2020;">Shipping:</strong> {{ number_format($order->shipping_cost, 2) }} AED</p>
                      <p style="margin:10px 0;"><strong style="color:#df2020;">VAT (AED):</strong> {{ number_format($order->tax_amount, 2) }} AED</p>
                      <p style="margin:10px 0;"><strong style="color:#df2020;">Total Paid:</strong> {{ number_format($order->total, 2) }} AED</p>
                    </td>
                  </tr>
                </table>

                <p style="margin:0 0 20px 0;">We will process your order and get back to you shortly with tracking information.</p>

                <!-- Button -->
                <table cellpadding="0" cellspacing="0" border="0" align="left" style="margin:20px 0;">
                  <tr>
                    <td bgcolor="#DF2020" align="center" style="border-radius:5px; padding:12px 30px;">
                      <a href="https://www.o2mart.net" style="font-family:Arial, sans-serif; font-size:16px; color:#ffffff; text-decoration:none; display:inline-block; font-weight:bold;">
                        Visit Our Store
                      </a>
                    </td>
                  </tr>
                </table>

                <div style="clear:both; height:10px;"></div>

                <p style="margin:0 0 20px 0;">We appreciate your trust in O2Mart – making car care easy, reliable, and hassle-free in the UAE.</p>

                <p style="margin:0 0 10px 0;">Best regards,<br/>The O2Mart Team<br/>
                  <a href="https://www.o2mart.net" style="color:#df2020; text-decoration:none;">www.o2mart.net</a> | +971 56 178 7270
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td bgcolor="#DF2020" align="center" style="color:#ffffff; padding:15px 20px; font-size:12px; font-family:Arial, sans-serif;">
                <p style="margin:0; font-weight:bold;">Follow us:</p>
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:10px auto;">
                  <tr>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.linkedin.com/company/o2mart/" target="_blank">
                        <img src="https://o2mart.to7fa.online/email-assets/linkedin.png?v=5" width="24" height="24" alt="LinkedIn" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.facebook.com/o2mart" target="_blank">
                        <img src="https://o2mart.to7fa.online/email-assets/facebook.png?v=5" width="24" height="24" alt="Facebook" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.instagram.com/o2mart/" target="_blank">
                        <img src="https://o2mart.to7fa.online/email-assets/instagram.png?v=5" width="24" height="24" alt="Instagram" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://wa.me/971561787270" target="_blank">
                        <img src="https://o2mart.to7fa.online/email-assets/whatsapp.png?v=5" width="24" height="24" alt="WhatsApp" border="0" style="display:block;" />
                      </a>
                    </td>
                  </tr>
                </table>
                <p style="margin:0;">&copy; {{ date('Y') }} O2Mart. All rights reserved.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>

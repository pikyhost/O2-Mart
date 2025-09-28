<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Special Coupon</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Email container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        /* Header */
        .header {
            background-color: #000000;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
        }

        /* Content */
        .content {
            padding: 30px 20px;
        }

        /* Coupon box */
        .coupon-box {
            background-color: #fff8f0;
            border: 2px dashed #FF6B00;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .coupon-code {
            font-size: 28px;
            font-weight: bold;
            color: #FF6B00;
            letter-spacing: 2px;
            margin: 10px 0;
        }

        /* Details */
        .details {
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
            color: #000000;
            width: 150px;
        }

        .detail-value {
            flex: 1;
        }

        /* Footer */
        .footer {
            background-color: #000000;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }

        .footer a {
            color: #FF6B00;
            text-decoration: none;
        }

        /* Button */
        .cta-button {
            display: inline-block;
            background-color: #FF6B00;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .coupon-code {
                font-size: 24px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="header">
        <h1>Your Special Discount</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Hello,</p>
        <p>Thank you for subscribing to our Popup! Here's your exclusive coupon code to use on your next purchase.</p>

        <!-- Coupon Box -->
        <div class="coupon-box">
            <div>Use this code at checkout:</div>
            <div class="coupon-code">{{ $coupon->code }}</div>
            <div>for
                @if($coupon->type === 'percentage')
                    {{ $coupon->value }}% off
                @else
                    {{ $coupon->value }} {{ \App\Models\Setting::getCurrency()?->symbol ?? '$' }} off
                @endif
            </div>
        </div>

        <!-- Coupon Details -->
        <div class="details">
            <div class="detail-row">
                <div class="detail-label">Discount Value:</div>
                <div class="detail-value">
                    @if($coupon->type === 'percentage')
                        {{ $coupon->value }}% off your order
                    @else
                        {{ $coupon->value }} {{ \App\Models\Setting::getCurrency()?->symbol ?? '$' }} off your order
                    @endif
                </div>
            </div>

            @if($coupon->min_order_amount)
                <div class="detail-row">
                    <div class="detail-label">Minimum Order:</div>
                    <div class="detail-value">{{ $coupon->min_order_amount }} {{ \App\Models\Setting::getCurrency()?->symbol ?? '$' }}</div>
                </div>
            @endif

            <div class="detail-row">
                <div class="detail-label">Expiration Date:</div>
                <div class="detail-value">{{ $coupon->expires_at->format('F j, Y') }}</div>
            </div>
        </div>

        <!-- Call to Action -->
        <div style="text-align: center;">
            <a href="{{ config('app.frontend_url') }}/shop" class="cta-button">Shop Now</a>
        </div>

        <p>Simply enter the code at checkout to apply your discount. This offer is valid until the expiration date shown above.</p>
        <p>Happy shopping!</p>
        <p>The {{ config('app.name') }} Team</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>If you did not request this email, please ignore it.</p>
    </div>
</div>
</body>
</html>

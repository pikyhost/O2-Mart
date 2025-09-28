<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('thank_you') }}</title>

    <!-- Include FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .website-name {
            font-size: 28px;
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
            color: #1877F2; /* Facebook Blue */
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            display: inline-block;
            background: linear-gradient(45deg, #1877F2, #1DA1F2, #E4405F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 2px solid #ddd;
        }

        .contact-info {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .contact-info i {
            margin-right: 8px;
            color: #E4405F;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }

        .social-icons a {
            font-size: 36px; /* Bigger icons */
            color: inherit; /* Use default brand colors */
            text-decoration: none;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            opacity: 0.8;
        }

        /* Brand Colors */
        .social-icons .facebook { color: #1877F2; }
        .social-icons .x { color: #000000; }
        .social-icons .instagram { color: #E4405F; }
        .social-icons .youtube { color: #FF0000; }
        .social-icons .linkedin { color: #0077B5; }
        .social-icons .whatsapp { color: #25D366; }
        .social-icons .snapchat { color: #FFFC00; }
        .social-icons .tiktok { color: #000000; }
        .social-icons .telegram { color: #0088CC; }
    </style>

    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #d63384;
            text-align: center;
            margin-bottom: 5px;
        }
        .website-name {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin: 10px 0;
        }


        .order-item:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .order-item .product-details strong {
            color: #d63384;
        }


        .order-item img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        strong {
            color: #d63384;
        }
        .color-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        .social-icons {
            margin-top: 10px;
        }
        .social-icons a {
            margin: 0 8px;
            display: inline-block;
        }
        .social-icons img {
            width: 28px;
            height: 28px;
            transition: transform 0.3s ease;
        }
        .social-icons img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<@php $locale = app()->getLocale(); @endphp

<div class="container">
    <h1 class="website-name">
        {{ \App\Models\Setting::getSetting('site_name') }}
    </h1>

    <h3>{{ __('thank_you') }}</h3>
    @if($order->user_id)
        <p><strong>{{ __('Customer Name') }}:</strong> {{ $order->user->name }}</p>
    @elseif($order->contact_id)
        <p><strong>{{ __('Contact Name') }}:</strong> {{ $order->contact->name }}</p>
    @endif
    <p><strong>{{ __('order_id') }}:</strong> {{ $order->id }}</p>
    <p><strong>{{ __('total_amount') }}:</strong> {{ number_format($order->total, 2) }}</p>
    <p><strong>{{ __('payment_method') }}:</strong> {{ $order->paymentMethod->name ?? 'N/A' }}</p>

    <h2>{{ __('order_details') }}:</h2>
    <div class="order-details"
         style="display: flex; flex-direction: column; gap: 20px; padding: 15px; border: 2px solid #f8a3c4; border-radius: 10px; background: #fff5f9;">

        @foreach ($order->items as $item)

            <div class="order-item"
                 style="display: flex; align-items: center; width: 100%;
                    flex-direction: {{ app()->getLocale() == 'ar' ? 'row-reverse' : 'row' }}; gap: 15px;">
                <!-- Item Number -->

                <!-- Product Image -->
                @if($item->product->getFeatureProductImageUrl())
                    <div style="flex-shrink: 0; order: {{ app()->getLocale() == 'ar' ? '1' : '2' }};">
                        <img src="{{ $item->product->getFeatureProductImageUrl() }}"
                             alt="{{ $item->product->getTranslation('name', app()->getLocale()) }}"
                             style="width: 120px; height: auto; border-radius: 5px;">
                    </div>
                @endif

                <!-- Product Details -->
                <div style="flex: 1; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; order: {{ app()->getLocale() == 'ar' ? '2' : '1' }};">
                    <h3><strong><span style="color: black !important;">#{{ $loop->iteration }}</span></strong></h3>
                    <p><strong>{{ __('product') }}:</strong> {{ $item->product->getTranslation('name', app()->getLocale()) }}</p>
                    <p><strong>{{ __('quantity') }}:</strong> {{ $item->quantity }}</p>
                    <p><strong>{{ __('price') }}:</strong> {{ number_format($item->subtotal, 2) }}</p>

                    @if($item->product->productColors->isNotEmpty())
                        @if($item->size_id)
                            <p><strong>{{ __('size') }}:</strong> {{ $item->size->name }}</p>
                        @endif

                        <p><strong>{{ __('color') }}:</strong>
                            @foreach($item->product->productColors as $productColor)
                                <span class="color-box"
                                      style="background-color:{{ $productColor->color->code }};">
                                </span> {{ $productColor->color->name }}
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @php
        $contact = \App\Models\Setting::getContactDetails();
    @endphp
    <div style="padding: 15px; background: #f0f8ff; border-left: 5px solid #007bff; border-radius: 5px;">
        <p style="font-size: 18px; font-weight: bold; color: #333;">{{ __('order_status') }}</p>
        <p style="font-size: 16px; color: #555;">{{ $statusMessage }}</p>
    </div>

    <div class="footer">
        <div class="contact-info">
            <p>
                <i class="fas fa-envelope"></i>
                {{ app()->getLocale() === 'ar' ? 'للتواصل عبر البريد الإلكتروني:' : 'Contact us via Email:' }}
                <a href="mailto:{{ $contact['email'] }}">
                    {{ $contact['email'] }}
                </a>
            </p>
            <p>
                <i class="fas fa-phone"></i>
                {{ app()->getLocale() === 'ar' ? 'للتواصل عبر الهاتف:' : 'Contact us via Phone:' }}
                <a href="tel:{{ $contact['phone'] }}">
                    {{ $contact['phone'] }}
                </a>
            </p>
        </div>


        <div class="social-icons">
            @foreach (\App\Models\Setting::getSocialMediaLinks() as $platform => $link)
                @if ($link)
                    <a href="{{ $link }}" target="_blank" class="{{ strtolower($platform) }}">
                        @if(strtolower($platform) === 'x')
                            <i class="fa-brands fa-x-twitter"></i>
                        @else
                            <i class="fab fa-{{ strtolower($platform) }}"></i>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>

    </div>
</div>
</body>
</html>

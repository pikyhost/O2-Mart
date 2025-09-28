@php
    $contact = \App\Models\Setting::getContactDetails();
    $siteSettings = App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();
    $siteName = $siteSettings["site_name"] ?? ($locale === 'ar' ? 'لا يوجد شعار بعد' : 'No Logo Yet');
@endphp

    <!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __("Invitation For Join Us") }} {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf2f8; /* Light Pink Background */
            margin: 0;
            padding: 0;
            color: #4b5563; /* Dark Gray Text */
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff; /* White Background */
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #fbcfe8; /* Soft Pink Border */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ec4899; /* Pink Header */
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #fbcfe8;
            margin-top: 20px;
            color: #6b7280; /* Muted Gray Text */
            font-size: 12px;
        }
        .footer a {
            color: #9333ea; /* Purple Accent */
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>

    <style>
        .website-name {
            font-size: 36px; /* Bigger font size */
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
            color: #1877F2; /* Facebook Blue */
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            display: block;
            text-align: center; /* Centered text */
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
        .social-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Spacing between links */
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }

        .social-links a {
            font-size: 16px;
            color: #1877F2; /* Default link color */
            text-decoration: none;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: background 0.3s, color 0.3s;
        }

        .social-links a:hover {
            background-color: #1877F2;
            color: #ffffff;
        }
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
            font-size: 36px; /* Bigger font size */
            font-weight: bold;
            color: #555;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin: 10px 0;
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
    </style>
</head>
<body>
<div class="container">
    <h1 class="website-name">{{ $siteName }}</h1>

    <h2>{{ __("You're Invited!") }}</h2>

    <p>{{ __("We are excited to invite you to explore our extensive e-commerce platform, where shopping meets convenience. Join us and access a wide range of products anytime, anywhere!") }}</p>

    <p><strong>{{ __("Why Join?") }}</strong></p>
    <ul>
        <li>{{ __("Access thousands of products in various categories.") }}</li>
        <li>{{ __("Enjoy a seamless shopping experience with our user-friendly interface.") }}</li>
        <li>{{ __("Stay updated with the latest trends and exclusive offers.") }}</li>
    </ul>

    <p>{{ __("Click the button below to start your journey with us:") }}</p>

    <div style="text-align: center; margin: 20px 0;">
        <a href="{{ $acceptUrl }}" style="
            display: inline-block;
            background-color: #d63384;
            color: #ffffff;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        " onmouseover="this.style.background='#a82664'" onmouseout="this.style.background='#d63384'">
            {{ __("Join Now") }}
        </a>
    </div>

    <p>{{ __("We look forward to having you with us!") }}</p>
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

        <div class="social-links">
            @foreach (\App\Models\Setting::getSocialMediaLinks() as $platform => $link)
                @if ($link)
                    <a href="{{ $link }}" target="_blank">
                        {{ ucfirst($platform) }} <!-- Display platform name as text -->
                    </a>
                @endif
            @endforeach
        </div>
    </div>

</div>
</body>
</html>

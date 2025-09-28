<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #d1d5db;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
        }

        .content {
            padding: 20px 0;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #111827;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #000000;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #d1d5db;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }

        .footer a {
            color: #1f2937;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
                color: #e5e7eb;
            }

            .container {
                background-color: #1f2937;
                border-color: #374151;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            }

            .header h1 {
                color: #f9fafb;
            }

            .btn {
                background-color: #e5e7eb;
                color: #1f2937;
            }

            .btn:hover {
                background-color: #ffffff;
            }

            .footer {
                color: #9ca3af;
                border-top-color: #4b5563;
            }

            .footer a {
                color: #f9fafb;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>You're Invited to Join {{ config('app.name') }}</h1>
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>You have been invited to join <strong>{{ config('app.name') }}</strong> as a
            <strong>{{ \App\Enums\UserRole::getLabelFor($invitation->role->name) }}</strong>.</p>
        <p>Click the button below to accept your invitation:</p>
        <a href="{{ $acceptUrl }}" class="btn">Accept Invitation</a>
    </div>
    <div class="footer">
        <p>If you weren’t expecting this invitation, you can safely ignore this email.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p><a href="{{ config('app.url') }}">Visit our website</a></p>
    </div>
</div>
</body>
</html>

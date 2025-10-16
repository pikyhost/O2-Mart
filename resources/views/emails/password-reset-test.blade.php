<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset – O2Mart</title>
    <style>
        /* بداية التنسيق العام */
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

        /* تحسينات خاصة بالـ Outlook */
        table {
            border-spacing: 0;
            width: 100%;
        }
        td {
            padding: 0;
        }

        /* الهيدر */
        .header {
            background-color: #df2020;
            padding: 15px 20px;
        }
        .header img {
            width: 95px;
            height: auto;
            vertical-align: middle;
            margin-bottom: 3px;
        }
        .header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 25px;
            vertical-align: middle;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
        }
        .header a {
            color: #ffffff;
            text-decoration: none;
        }
        .header a:hover {
            text-decoration: underline;
        }

        /* المحتوى */
        .content {
            padding: 30px 20px;
        }
        .reset-button {
            background-color: #df2020 !important;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none !important;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
            border: none;
            mso-style-priority: 99;
        }

        /* الفوتر */
        .footer {
            background-color: #df2020;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
            font-size: 12px;
        }
        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }
        .footer p {
            margin: 10px 0;
        }

        /* الروابط الاجتماعية */
        .social-links {
            text-align: center;
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            text-decoration: none;
            margin: 0 7px;
        }
        .social-links img {
            width: 20px;
            height: 20px;
            display: inline-block;
            vertical-align: middle;
        }

        /* نص الرابط */
        .link-text {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            word-break: break-all;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- الهيدر -->
        <table class="header" role="presentation" style="background-color: #df2020; padding: 15px 20px; width: 100%;">
            <tr>
                <td style="vertical-align: middle;">
                    <a href="https://www.o2mart.net" target="_blank">
                        <img src="https://i.ibb.co/0VXXGcfy/logo.png" alt="logo" border="0" style="width: 95px; height: auto; vertical-align: middle; margin-bottom: 3px;" />
                    </a>
                </td>
                <td style="vertical-align: middle;">
                    <h2 style="color: #ffffff; margin: 0; font-size: 25px;">– Your Trusted Auto Parts Partner</h2>
                </td>
            </tr>
        </table>

        <!-- المحتوى -->
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset the password for your O2Mart account.</p>
            <table role="presentation" style="margin: 20px 0;">
                <tr>
                    <td style="background-color: #df2020; border-radius: 5px;">
                        <a href="{{ config('app.frontend_url') }}/reset-password/{{ $token }}?email={{ $email }}" style="background-color: #df2020; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Reset Password</a>
                    </td>
                </tr>
            </table>
            <p>Please note that this link will expire in 60 minutes.</p>
            <p>If you did not request a password reset, you can safely ignore this email—no further action is needed.</p>
            <p>Best regards,<br>
            The O2Mart Team<br>
            <a href="https://www.o2mart.net">www.o2mart.net</a> | +971 56 178 7270</p>
            <p>If you're having trouble clicking the "Reset Password" button, copy and paste the link below into your web browser:</p>
            <div class="link-text">{{ config('app.frontend_url') }}/reset-password/{{ $token }}?email={{ $email }}</div>
        </div>

        <!-- الفوتر -->
        <table class="footer" role="presentation">
            <tr>
                <td>
                    <p style="font-weight: bold; margin-bottom: 10px;">Follow us:</p>
                    <div class="social-links" style="text-align: center; margin: 15px 0;">
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/company/o2mart/" target="_blank" style="display: inline-block; text-decoration: none; margin: 0 7px;">
                            <img src="https://i.ibb.co/bjRrfjKJ/icons8-linkedin-24.png" alt="LinkedIn" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;" />
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/o2mart" target="_blank" style="display: inline-block; text-decoration: none; margin: 0 7px;">
                            <img src="https://i.ibb.co/nNcJpzQw/icons8-facebook-logo-50.png" alt="Facebook" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;" />
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/o2mart/" target="_blank" style="display: inline-block; text-decoration: none; margin: 0 7px;">
                            <img src="https://i.ibb.co/F4xXdtXp/icons8-instagram-logo-50-1.png" alt="Instagram" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;" />
                        </a>
                        <!-- WhatsApp -->
                        <a href="https://wa.me/971561787270" target="_blank" style="display: inline-block; text-decoration: none; margin: 0 7px;">
                            <img src="https://i.ibb.co/20ZJMSmx/icons8-whatsapp-50.png" alt="WhatsApp" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;" />
                        </a>
                    </div>
                    <p style="margin-top: 10px;">&copy; {{ date('Y') }} O2Mart. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
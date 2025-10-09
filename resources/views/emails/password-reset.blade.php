<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset – O2Mart</title>
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
        .content {
            padding: 30px 20px;
        }
        .reset-button {
            background-color: #df2020;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
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
    <div class="header">
        <a href="https://www.o2mart.net" target="_blank" style="display: inline-block">
            <img src="https://i.ibb.co/0VXXGcfy/logo.png" alt="logo" border="0" />
        </a>
        <h2>– Your Trusted Auto Parts Partner</h2>
    </div>

    <div class="content">
        <p>Hello,</p>
        
        <p>We received a request to reset the password for your O2Mart account.</p>
        
        <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
        
        <p>Please note that this link will expire in 60 minutes.</p>
        
        <p>If you did not request a password reset, you can safely ignore this email—no further action is needed.</p>
        
        <p>Best regards,<br>
        The O2Mart Team<br>
        <a href="www.o2mart.net">www.o2mart.net</a> | +971 56 178 7270</p>
        
        <p>If you're having trouble clicking the "Reset Password" button, copy and paste the link below into your web browser:</p>
        <div class="link-text">{{ $resetUrl }}</div>
    </div>

    <div class="footer" style="text-align: center; padding: 10px 0; background-color: #df2020">
        <p style="font-weight: bold">Follow us:</p>
        
        <div style="display: flex; justify-content: center; align-items: center">
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
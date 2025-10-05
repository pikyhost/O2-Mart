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
            padding: 10px 15px;
            text-align: center;
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
        <h1><a href="https://mk3bel.o2mart.net/">O2Mart</a> – Your Trusted Auto Parts Partner</h1>
    </div>

    <div class="content">
        <p>Hello,</p>
        
        <p>We received a request to reset the password for your O2Mart account.</p>
        
        <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
        
        <p>Please note that this link will expire in 60 minutes.</p>
        
        <p>If you did not request a password reset, you can safely ignore this email—no further action is needed.</p>
        
        <p>Regards,<br>
        O2Mart Team</p>
        
        <p>If you're having trouble clicking the "Reset Password" button, copy and paste the link below into your web browser:</p>
        <div class="link-text">{{ $resetUrl }}</div>
    </div>

    <div class="footer">
        <p><strong>Follow us:</strong></p>
        <p>
            <a href="https://www.linkedin.com/company/o2mart/">LinkedIn: O2Mart</a> | 
            <a href="https://www.facebook.com/o2mart">FB: O2Mart | Dubai</a> | 
            <a href="https://www.instagram.com/o2mart/">IG: @o2mart</a>
        </p>
        <p>&copy; {{ date('Y') }} O2Mart. All rights reserved.</p>
    </div>
</div>
</body>
</html>
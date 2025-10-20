<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Templates Preview - O2Mart</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 {
            color: #DF2020;
            font-size: 32px;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .email-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .email-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 24px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .email-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-color: #DF2020;
        }
        .email-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .email-title {
            font-size: 18px;
            font-weight: 600;
            color: #DF2020;
            margin-bottom: 8px;
        }
        .email-description {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 24px;
            }
            h1 {
                font-size: 24px;
            }
            .email-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Templates Preview</h1>
        <p class="subtitle">Click any template to view its design in the browser</p>
        
        <div class="email-grid">
            <a href="{{ url('/test-email/password-reset') }}" class="email-card">
                <div class="email-icon">🔑</div>
                <div class="email-title">Password Reset</div>
                <div class="email-description">View password reset email template with reset link</div>
            </a>
            
            <a href="{{ url('/test-email/verify-email') }}" class="email-card">
                <div class="email-icon">✉️</div>
                <div class="email-title">Email Verification</div>
                <div class="email-description">View email verification template with confirmation link</div>
            </a>
            
            <a href="{{ url('/test-email/coupon') }}" class="email-card">
                <div class="email-icon">🎟️</div>
                <div class="email-title">Coupon Code</div>
                <div class="email-description">View coupon email with discount code and details</div>
            </a>
            
            <a href="{{ url('/test-email/inquiry-confirmation') }}" class="email-card">
                <div class="email-icon">📝</div>
                <div class="email-title">Inquiry Confirmation</div>
                <div class="email-description">View inquiry confirmation email with next steps</div>
            </a>
            
            <a href="{{ url('/test-email/receipt') }}" class="email-card">
                <div class="email-icon">🧾</div>
                <div class="email-title">Order Receipt</div>
                <div class="email-description">View order receipt with items and payment details</div>
            </a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} O2Mart - Email Templates Testing</p>
            <p style="margin-top: 8px;">All templates use consistent branding and design</p>
        </div>
    </div>
</body>
</html>

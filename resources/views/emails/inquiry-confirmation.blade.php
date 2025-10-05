<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We've received your inquiry # {{ $inquiry->id }} – O2Mart is on it!</title>
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
            padding: 20px 20px;
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
        .steps {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .step {
            margin-bottom: 15px;
        }
        .step strong {
            color: #df2020;
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
            padding: 15px;
            text-align: center;
            font-size: 14px;
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
        <h1><a href="https://mk3bel.o2mart.net/">O2Mart – Your Trusted Auto Parts Partner</a></h1>
    </div>

    <div class="content">
        <p>Hi {{ explode(' ', $inquiry->full_name)[0] }},</p>
        
        <p>Thanks for reaching out to O2Mart! We've received your inquiry # {{ $inquiry->id }} and our team is already working on it.</p>
        
        <p><strong>Here's what happens next:</strong></p>
        
        <div class="steps">
            <div class="step">
                <strong>Review</strong> – Our specialists will check your request against our supplier network.
            </div>
            <div class="step">
                <strong>Match</strong> – We'll find the best options for your part(s).
            </div>
            <div class="step">
                <strong>Reply</strong> – You'll receive a response as soon as possible with availability, pricing, and delivery options.
            </div>
        </div>
        
        <p>In the meantime, if you'd like faster service, you can also reach us directly on WhatsApp:</p>
        <a href="https://wa.me/971501234567" class="whatsapp-link">Contact us on WhatsApp</a>
        
        <p>We appreciate your trust in O2Mart – making car care easy, reliable, and hassle-free in the UAE.</p>
        
        <p>Best regards,<br>
        The O2Mart Team<br>
        <a href="https://o2mart.ae">www.o2mart.ae</a> | +971 50 123 4567</p>
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
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Your Special Coupon – O2Mart</title>
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
                      – Your Special Discount
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td style="padding:30px 25px; font-family:Arial, sans-serif; color:#333333; font-size:15px; line-height:1.6;">
                <p style="margin:0 0 15px 0;">Hello,</p>
                <p style="margin:0 0 20px 0;">Thank you for subscribing to our offer! Here's your exclusive coupon code to use on your next purchase.</p>

                <!-- Coupon Box -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color:#f8f9fa; border:2px dashed #df2020; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td align="center">
                      <p style="margin:0 0 10px 0; font-size:14px;">Use this code at checkout:</p>
                      <p style="margin:10px 0; font-size:28px; font-weight:bold; color:#df2020; letter-spacing:2px;">{{ $coupon->code }}</p>
                      <p style="margin:10px 0 0 0; font-size:14px;">
                        @if($coupon->type === 'discount_percentage')
                          for {{ $coupon->value }}% off
                        @elseif($coupon->type === 'discount_amount')
                          for {{ $coupon->value }} AED off
                        @elseif($coupon->type === 'free_shipping')
                          for Free Shipping
                        @endif
                      </p>
                    </td>
                  </tr>
                </table>

                <!-- Details -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color:#f8f9fa; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td>
                      <p style="margin:0 0 10px 0;"><strong style="color:#df2020;">Discount Value:</strong>
                        @if($coupon->type === 'discount_percentage')
                          {{ $coupon->value }}% off your order
                        @elseif($coupon->type === 'discount_amount')
                          {{ $coupon->value }} AED off your order
                        @elseif($coupon->type === 'free_shipping')
                          Free Shipping on your order
                        @endif
                      </p>
                      @if($coupon->min_order_amount)
                      <p style="margin:0 0 10px 0;"><strong style="color:#df2020;">Minimum Order:</strong> {{ $coupon->min_order_amount }} AED</p>
                      @endif
                      @if($coupon->expires_at)
                      <p style="margin:0;"><strong style="color:#df2020;">Expiration Date:</strong> {{ $coupon->expires_at->format('F j, Y') }}</p>
                      @else
                      <p style="margin:0;"><strong style="color:#df2020;">Expiration:</strong> No expiration date</p>
                      @endif
                    </td>
                  </tr>
                </table>

                <p style="margin:0 0 20px 0;">Simply enter the code at checkout to apply your discount. Start shopping now:</p>

                <!-- Button -->
                <table cellpadding="0" cellspacing="0" border="0" align="left" style="margin:20px 0;">
                  <tr>
                    <td bgcolor="#DF2020" align="center" style="border-radius:5px; padding:12px 30px;">
                      <a href="https://www.o2mart.net/shop" style="font-family:Arial, sans-serif; font-size:16px; color:#ffffff; text-decoration:none; display:inline-block; font-weight:bold;">
                        Shop Now
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

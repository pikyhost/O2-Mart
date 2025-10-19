<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Verify Email Address – O2Mart</title>
  </head>
  <body style="margin:0; padding:0; background-color:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f5f5f5">
      <tr>
        <td align="center">
          <!-- Main Container -->
          <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="width:600px; max-width:600px; margin:0 auto;">
            
            <!-- Header -->
            <tr>
              <td bgcolor="#df2020" style="padding:15px 20px;" align="left">
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td valign="middle" align="left">
                        <a href="https://www.o2mart.net" target="_blank" style="display: inline-block">
            <img src="https://i.ibb.co/0VXXGcfy/logo.png" alt="logo" border="0" />
        </a>
                    </td>
                    <td valign="middle" align="left" style="color:#ffffff; font-size:20px; font-family:Arial, sans-serif; padding-left:10px;">
                      – Your Trusted Auto Parts Partner
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td style="padding:30px 25px; font-family:Arial, sans-serif; color:#333333; font-size:15px; line-height:1.6;">
                <p style="margin:0 0 15px 0;">Hello!</p>
                <p style="margin:0 0 20px 0;">Please click the button below to verify your email address.</p>

                <!-- Button -->
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:20px auto;">
                  <tr>
                    <td bgcolor="#df2020" align="center" style="border-radius:5px; padding:12px 30px;">
                      <a href="{{ $verificationUrl }}"
                        style="font-family:Arial, sans-serif; font-size:16px; color:#ffffff; text-decoration:none; display:inline-block; font-weight:bold;">
                        Verify Email Address
                      </a>
                    </td>
                  </tr>
                </table>

                <p style="margin:0 0 20px 0;">If you did not create an account, no further action is required.</p>

                <p style="margin:0 0 10px 0;">Regards,<br/>O2Mart
                </p>

                <p style="margin:20px 0 10px 0;">If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
                <p style="background-color:#f8f9fa; padding:12px; border-radius:5px; word-break:break-all; font-size:13px;">
                  {{ $verificationUrl }}
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td bgcolor="#df2020" align="center" style="color:#ffffff; padding:15px 20px; font-size:12px; font-family:Arial, sans-serif;">
                <p style="margin:0; font-weight:bold;">Follow us:</p>
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:10px auto;">
                  <tr>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.linkedin.com/company/o2mart/" target="_blank">
                        <img src="https://i.ibb.co/bjRrfjKJ/icons8-linkedin-24.png" width="24" height="24" alt="LinkedIn" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.facebook.com/o2mart" target="_blank">
                        <img src="https://i.ibb.co/nNcJpzQw/icons8-facebook-logo-50.png" width="24" height="24" alt="Facebook" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://www.instagram.com/o2mart/" target="_blank">
                        <img src="https://i.ibb.co/F4xXdtXp/icons8-instagram-logo-50-1.png" width="24" height="24" alt="Instagram" border="0" style="display:block;" />
                      </a>
                    </td>
                    <td align="center" style="padding:0 5px;">
                      <a href="https://wa.me/971561787270" target="_blank">
                        <img src="https://i.ibb.co/20ZJMSmx/icons8-whatsapp-50.png" width="24" height="24" alt="WhatsApp" border="0" style="display:block;" />
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
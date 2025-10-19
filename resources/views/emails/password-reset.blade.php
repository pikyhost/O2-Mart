<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Password Reset – O2Mart</title>
  </head>
  <body
    style="
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333333;
    "
  >
    <table
      width="100%"
      cellpadding="0"
      cellspacing="0"
      border="0"
      bgcolor="#f5f5f5"
    >
      <tr>
        <td align="center">
          <table
            width="600"
            cellpadding="0"
            cellspacing="0"
            border="0"
            bgcolor="#ffffff"
            style="width: 600px; max-width: 600px"
          >
            <!-- Header -->
            <tr>
              <td bgcolor="#df2020" style="padding: 15px 20px">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td align="left" valign="middle" style="width: 110px">
                      <a href="https://www.o2mart.net" target="_blank">
                        <img
                          src="{{ asset('emails/social_media_images/logo.png') }}"
                          alt="O2Mart"
                          width="95"
                          style="display: block; border: 0"
                        />
                      </a>
                    </td>
                    <td
                      align="left"
                      valign="middle"
                      style="color: #ffffff; font-size: 20px; font-weight: bold"
                    >
                      – Your Trusted Auto Parts Partner
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Content -->
            <tr>
              <td style="padding: 30px 20px">
                <p>Hello,</p>
                <p>
                  We received a request to reset the password for your O2Mart
                  account.
                </p>

                <p>
                  <a
                    href="{{ config('app.frontend_url') }}/reset-password/{{ $token }}?email={{ $email }}"
                    style="
                      background-color: #df2020;
                      color: #ffffff;
                      padding: 12px 30px;
                      text-decoration: none;
                      border-radius: 4px;
                      display: inline-block;
                      font-weight: bold;
                    "
                  >
                    Reset Password
                  </a>
                </p>

                <p>Please note that this link will expire in 60 minutes.</p>
                <p>
                  If you did not request a password reset, you can safely ignore
                  this email—no further action is needed.
                </p>

                <p>
                  Best regards,<br />
                  The O2Mart Team<br />
                  <a
                    href="https://www.o2mart.net"
                    style="color: #df2020; text-decoration: none"
                    >www.o2mart.net</a
                  >
                  | +971 56 178 7270
                </p>

                <p>
                  If you're having trouble clicking the "Reset Password" button,
                  copy and paste the link below into your web browser:
                </p>
                <p
                  style="
                    background-color: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    word-break: break-all;
                    font-size: 14px;
                  "
                >
                  {{ config('app.frontend_url') }}/reset-password/{{ $token
                  }}?email={{ $email }}
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td
                bgcolor="#df2020"
                align="center"
                style="color: #ffffff; padding: 20px"
              >
                <p style="font-weight: bold; margin: 0 0 10px 0">Follow us:</p>

                <table
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  align="center"
                >
                  <tr>
                    <td align="center">
                      <a
                        href="https://www.linkedin.com/company/o2mart/"
                        target="_blank"
                        style="text-decoration: none"
                      >
                        <img
                          src="{{ asset('emails/social_media_images/linkedin.png') }}"
                          alt="LinkedIn"
                          width="40"
                          height="40"
                          style="margin: 0 5px; display: inline-block"
                        />
                      </a>
                      <a
                        href="https://www.facebook.com/o2mart"
                        target="_blank"
                        style="text-decoration: none"
                      >
                        <img
                          src="{{ asset('emails/social_media_images/facebook.png') }}"
                          alt="Facebook"
                          width="40"
                          height="40"
                          style="margin: 0 5px; display: inline-block"
                        />
                      </a>
                      <a
                        href="https://www.instagram.com/o2mart/"
                        target="_blank"
                        style="text-decoration: none"
                      >
                        <img
                          src="{{ asset('emails/social_media_images/instagram.png') }}"
                          alt="Instagram"
                          width="40"
                          height="40"
                          style="margin: 0 5px; display: inline-block"
                        />
                      </a>
                      <a
                        href="https://wa.me/971561787270"
                        target="_blank"
                        style="text-decoration: none"
                      >
                        <img
                          src="{{ asset('emails/social_media_images/whatsapp.png') }}"
                          alt="WhatsApp"
                          width="20"
                          height="26"
                          style="margin: 0 5px; display: inline-block"
                        />
                      </a>
                    </td>
                  </tr>
                </table>

                <p style="margin-top: 15px; font-size: 12px">
                  &copy; {{ date('Y') }} O2Mart. All rights reserved.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Password Reset – O2Mart</title>
  </head>
  <body style="margin: 0; padding: 0; background-color: #F5F5F5">
    <table
      width="100%"
      cellpadding="0"
      cellspacing="0"
      border="0"
      bgcolor="#F5F5F5"
    >
      <tr>
        <td align="center">
          <!-- Main Container -->
          <table
            width="600"
            cellpadding="0"
            cellspacing="0"
            border="0"
            bgcolor="#FFFFFF"
            style="width: 600px; max-width: 600px; margin: 0 auto"
          >
            
            <!-- Header -->
            <tr>
              <td bgcolor="#DF2020" style="padding: 15px 20px" align="left">
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td valign="middle" align="left">
                      <a
                        href="https://www.o2mart.net"
                        target="_blank"
                        style="display: inline-block"
                      >
                        <img
                          src="https://o2mart.to7fa.online/email-assets/logo.png?v=5"
                          alt="O2Mart Logo"
                          width="170"
                          height="50"
                          border="0"
                          style="display:block;"
                        />
                      </a>
                    </td>
                    <td
                      valign="middle"
                      align="left"
                      style="
                        color: #FFFFFF;
                        font-size: 20px;
                        font-family: Arial, sans-serif;
                        padding-left: 10px;
                      "
                    >
                      – Your Trusted Auto Parts Partner
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td
                style="
                  padding: 30px 25px;
                  font-family: Arial, sans-serif;
                  color: #333333;
                  font-size: 15px;
                  line-height: 1.6;
                "
              >
                <p style="margin: 0 0 15px 0">Hello,</p>
                <p style="margin: 0 0 20px 0">
                  We received a request to reset the password for your O2Mart
                  account.
                </p>

                <!-- Button (left aligned + forces next line) -->
                <table
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  align="left"
                  style="margin: 20px 0"
                >
                  <tr>
                    <td
                      bgcolor="#DF2020"
                      align="center"
                      style="border-radius: 5px; padding: 12px 30px"
                    >
                      <a
                        href="{{ config('app.frontend_url') }}/reset-password/{{ $token }}?email={{ $email }}"
                        style="
                          font-family: Arial, sans-serif;
                          font-size: 16px;
                          color: #FFFFFF;
                          text-decoration: none;
                          display: inline-block;
                          font-weight: bold;
                        "
                      >
                        Reset Password
                      </a>
                    </td>
                  </tr>
                </table>

                <div style="clear: both; height: 10px"></div>

                <p style="margin: 0 0 15px 0">
                  Please note that this link will expire in 60 minutes.
                </p>
                <p style="margin: 0 0 20px 0">
                  If you did not request a password reset, you can safely ignore
                  this email—no further action is needed.
                </p>

                <p style="margin: 0 0 10px 0">
                  Best regards,<br />The O2Mart Team<br />
                  <a
                    href="https://www.o2mart.net"
                    style="color: #DF2020; text-decoration: none"
                    >www.o2mart.net</a
                  >
                  | +971 56 178 7270
                </p>

                <p style="margin: 20px 0 10px 0">
                  If you're having trouble clicking the "Reset Password" button,
                  copy and paste the link below into your browser:
                </p>
                <p
                  style="
                    background-color: #F8F9FA;
                    padding: 12px;
                    border-radius: 5px;
                    word-break: break-all;
                    font-size: 13px;
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
                bgcolor="#DF2020"
                align="center"
                style="
                  color: #FFFFFF;
                  padding: 15px 20px;
                  font-size: 12px;
                  font-family: Arial, sans-serif;
                "
              >
                <p style="margin: 0; font-weight: bold">Follow us:</p>
                <table
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  align="center"
                  style="margin: 10px auto"
                >
                  <tr>
                    <td align="center" style="padding: 0 5px">
                      <a
                        href="https://www.linkedin.com/company/o2mart/"
                        target="_blank"
                      >
                        <img
                          src="https://o2mart.to7fa.online/email-assets/linkedin.png?v=5"
                          width="24"
                          height="24"
                          alt="LinkedIn"
                          border="0"
                          style="display: block"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a href="https://www.facebook.com/o2mart" target="_blank">
                        <img
                          src="https://o2mart.to7fa.online/email-assets/facebook.png?v=5"
                          width="24"
                          height="24"
                          alt="Facebook"
                          border="0"
                          style="display: block"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a
                        href="https://www.instagram.com/o2mart/"
                        target="_blank"
                      >
                        <img
                          src="https://o2mart.to7fa.online/email-assets/instagram.png?v=5"
                          width="24"
                          height="24"
                          alt="Instagram"
                          border="0"
                          style="display: block"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a href="https://wa.me/971561787270" target="_blank">
                        <img
                          src="https://o2mart.to7fa.online/email-assets/whatsapp.png?v=5"
                          width="24"
                          height="24"
                          alt="WhatsApp"
                          border="0"
                          style="display: block"
                        />
                      </a>
                    </td>
                  </tr>
                </table>
                <p style="margin: 0">
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
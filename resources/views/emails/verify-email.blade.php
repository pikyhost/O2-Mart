<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Verify Email Address – O2Mart</title>
    <!--[if mso]>
    <style type="text/css">
      body, table, td, a { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
  </head>
  <body style="margin: 0; padding: 0; background-color: #F5F5F5; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F5F5F5">
      <tr>
        <td align="center">
          <!-- Main Container -->
          <!--[if mso]>
          <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" style="width: 600px;">
          <![endif]-->
          <!--[if !mso]><!-->
          <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" style="width: 600px; max-width: 600px; margin: 0 auto;">
          <!--<![endif]-->
            
            <!-- Header -->
            <tr>
              <td bgcolor="#DF2020" style="padding: 15px 20px" align="left">
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td valign="middle" align="left">
                      <a href="https://www.o2mart.net" target="_blank" style="display: inline-block">
                        <img
                          src="{{ config('app.url') }}/photos/logo.png"
                          alt="O2Mart Logo"
                          width="150"
                          height="30"
                          border="0"
                          style="display: block; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;"
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
                <p style="margin: 0 0 15px 0">Hello!</p>
                <p style="margin: 0 0 20px 0">
                  Please click the button below to verify your email address.
                </p>

                <!-- Button -->
                <table
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  align="center"
                  style="margin: 20px auto;"
                >
                  <tr>
                    <td align="center" style="padding: 0;">
                      <!--[if mso]>
                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $verificationUrl }}" style="height:44px;v-text-anchor:middle;width:250px;" arcsize="11%" stroke="f" fillcolor="#DF2020">
                        <w:anchorlock/>
                        <center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">Verify Email Address</center>
                      </v:roundrect>
                      <![endif]-->
                      <!--[if !mso]><!-->
                      <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td
                            bgcolor="#DF2020"
                            align="center"
                            style="border-radius: 5px; padding: 12px 30px; -webkit-border-radius: 5px; -moz-border-radius: 5px;"
                          >
                            <a
                              href="{{ $verificationUrl }}"
                              style="
                                font-family: Arial, sans-serif;
                                font-size: 16px;
                                color: #FFFFFF;
                                text-decoration: none;
                                display: inline-block;
                                font-weight: bold;
                                line-height: 20px;
                              "
                            >
                              Verify Email Address
                            </a>
                          </td>
                        </tr>
                      </table>
                      <!--<![endif]-->
                    </td>
                  </tr>
                </table>

                <p style="margin: 0 0 20px 0">
                  If you did not create an account, no further action is required.
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
                  If you're having trouble clicking the "Verify Email Address" button,
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
                  {{ $verificationUrl }}
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
                          src="{{ config('app.url') }}/photos/linkedin.png"
                          width="24"
                          height="24"
                          alt="LinkedIn"
                          border="0"
                          style="display: block; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a href="https://www.facebook.com/o2mart" target="_blank">
                        <img
                          src="{{ config('app.url') }}/photos/FB.png"
                          width="24"
                          height="24"
                          alt="Facebook"
                          border="0"
                          style="display: block; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a
                        href="https://www.instagram.com/o2mart/"
                        target="_blank"
                      >
                        <img
                          src="{{ config('app.url') }}/photos/IG.png"
                          width="24"
                          height="24"
                          alt="Instagram"
                          border="0"
                          style="display: block; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;"
                        />
                      </a>
                    </td>
                    <td align="center" style="padding: 0 5px">
                      <a href="https://wa.me/971561787270" target="_blank">
                        <img
                          src="{{ config('app.url') }}/photos/WA.png"
                          width="28"
                          height="28"
                          alt="WhatsApp"
                          border="0"
                          style="display: block; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;"
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
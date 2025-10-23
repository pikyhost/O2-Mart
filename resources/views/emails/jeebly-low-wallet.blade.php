<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Jeebly Wallet Balance Low – O2Mart</title>
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
                      – System Alert
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
                <p style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold; color: #DF2020;">
                  ⚠️ Urgent: Jeebly Wallet Balance is Low
                </p>
                
                <p style="margin: 0 0 20px 0">
                  The Jeebly shipping integration has failed due to insufficient wallet balance. Immediate action is required to restore shipping functionality.
                </p>

                <!-- Error Box -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px; margin: 20px 0;">
                  <tr>
                    <td>
                      <p style="margin: 0 0 10px 0; font-weight: bold; color: #856404;">Error Details:</p>
                      <p style="margin: 0 0 5px 0; font-size: 14px;">
                        <strong>Status:</strong> {{ $errorData['status'] ?? 'Unknown' }}
                      </p>
                      <p style="margin: 0 0 5px 0; font-size: 14px;">
                        <strong>Message:</strong> {{ $errorData['message'] ?? 'Wallet balance is low' }}
                      </p>
                      @if(isset($errorData['order_id']))
                      <p style="margin: 0 0 5px 0; font-size: 14px;">
                        <strong>Failed Order ID:</strong> #{{ $errorData['order_id'] }}
                      </p>
                      @endif
                      <p style="margin: 10px 0 0 0; font-size: 14px;">
                        <strong>Time:</strong> {{ now()->format('d M Y, h:i A') }}
                      </p>
                    </td>
                  </tr>
                </table>

                <!-- Impact Box -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color: #f8d7da; border-left: 4px solid #dc3545; border-radius: 8px; margin: 20px 0;">
                  <tr>
                    <td>
                      <p style="margin: 0 0 10px 0; font-weight: bold; color: #721c24;">⚠️ Service Impact:</p>
                      <p style="margin: 0 0 5px 0; font-size: 14px; color: #721c24;">
                        • Shipment creation is currently failing<br/>
                        • New orders cannot be processed for shipping<br/>
                        • Customer delivery commitments may be affected
                      </p>
                    </td>
                  </tr>
                </table>

                <p style="margin: 0 0 10px 0; font-weight: bold;">Required Actions:</p>

                <!-- Actions -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 8px; margin: 20px 0;">
                  <tr>
                    <td>
                      <p style="margin: 0 0 10px 0; font-size: 14px;">
                        <strong style="color: #0c5460;">1. Recharge Jeebly Wallet</strong><br/>
                        Log in to your Jeebly account and add funds to the wallet immediately.
                      </p>
                      <p style="margin: 10px 0 10px 0; font-size: 14px;">
                        <strong style="color: #0c5460;">2. Verify Service Status</strong><br/>
                        Test the integration after recharging to ensure shipments are processing normally.
                      </p>
                      <p style="margin: 10px 0 0 0; font-size: 14px;">
                        <strong style="color: #0c5460;">3. Process Pending Orders</strong><br/>
                        Review and manually process any orders that failed during this period.
                      </p>
                    </td>
                  </tr>
                </table>

                <!-- Button -->
                <table
                  cellpadding="0"
                  cellspacing="0"
                  border="0"
                  align="left"
                  style="margin: 20px 0;"
                >
                  <tr>
                    <td align="center" style="padding: 0;">
                      <!--[if mso]>
                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ config('app.url') }}/admin" style="height:44px;v-text-anchor:middle;width:200px;" arcsize="11%" stroke="f" fillcolor="#DF2020">
                        <w:anchorlock/>
                        <center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">Go to Admin Panel</center>
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
                              href="{{ config('app.url') }}/admin"
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
                              Go to Admin Panel
                            </a>
                          </td>
                        </tr>
                      </table>
                      <!--<![endif]-->
                    </td>
                  </tr>
                </table>

                <!-- Spacer -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td height="10" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                  </tr>
                </table>

                <p style="margin: 0 0 20px 0; font-size: 13px; color: #666;">
                  This is an automated alert from the O2Mart system. Please address this issue as soon as possible to prevent service disruption.
                </p>

                <p style="margin: 0 0 10px 0">
                  Best regards,<br />O2Mart System<br />
                  <a
                    href="https://www.o2mart.net"
                    style="color: #DF2020; text-decoration: none"
                    >www.o2mart.net</a
                  >
                  | +971 56 178 7270
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

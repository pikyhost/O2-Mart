<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>We've received your inquiry – O2Mart</title>
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
                <p style="margin: 0 0 15px 0">Hi {{ explode(' ', $inquiry->full_name)[0] }},</p>
                <p style="margin: 0 0 20px 0">
                  Thanks for reaching out to O2Mart! We've received your inquiry #{{ $inquiry->id }} and our team is already working on it.
                </p>

                <!-- Inquiry Details -->
                <table width="100%" cellpadding="15" cellspacing="0" border="0" style="background-color:#f8f9fa; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td>
                      <p style="margin:0 0 15px 0;"><strong style="color:#df2020;">Inquiry Details:</strong></p>
                      <p style="margin:0 0 10px 0;"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $inquiry->type)) }}</p>
                      
                      @if($inquiry->car_make || $inquiry->car_model || $inquiry->car_year || $inquiry->vin_chassis_number)
                      <p style="margin:10px 0 5px 0;"><strong style="color:#df2020;">Vehicle Information:</strong></p>
                      @if($inquiry->car_make)
                      <p style="margin:0 0 5px 0;"><strong>Car Make:</strong> {{ $inquiry->car_make }}</p>
                      @endif
                      @if($inquiry->car_model)
                      <p style="margin:0 0 5px 0;"><strong>Car Model:</strong> {{ $inquiry->car_model }}</p>
                      @endif
                      @if($inquiry->car_year)
                      <p style="margin:0 0 5px 0;"><strong>Car Year:</strong> {{ $inquiry->car_year }}</p>
                      @endif
                      @if($inquiry->vin_chassis_number)
                      <p style="margin:0 0 10px 0;"><strong>VIN/Chassis Number:</strong> {{ $inquiry->vin_chassis_number }}</p>
                      @endif
                      @endif

                      @if($inquiry->type === 'tires' && ($inquiry->front_width || $inquiry->front_height || $inquiry->front_diameter || $inquiry->rear_tyres))
                      <p style="margin:10px 0 5px 0;"><strong style="color:#df2020;">Tire Sizes:</strong></p>
                      @if($inquiry->front_width || $inquiry->front_height || $inquiry->front_diameter)
                      <p style="margin:0 0 5px 0;"><strong>Front Tires:</strong> {{ $inquiry->front_width }}/{{ $inquiry->front_height }} R{{ $inquiry->front_diameter }}</p>
                      @endif
                      @if($inquiry->rear_tyres && is_array($inquiry->rear_tyres) && count($inquiry->rear_tyres) > 0)
                      <p style="margin:0 0 5px 0;"><strong>Rear Tires:</strong></p>
                      @foreach($inquiry->rear_tyres as $index => $rearTyre)
                      <p style="margin:0 0 5px 0;">  • Rear Set {{ $index + 1 }}: {{ $rearTyre['width'] ?? '' }}/{{ $rearTyre['height'] ?? '' }} R{{ $rearTyre['diameter'] ?? '' }} (Qty: {{ $rearTyre['quantity'] ?? 1 }})</p>
                      @endforeach
                      @endif
                      @endif

                      @if($inquiry->required_parts && count($inquiry->required_parts) > 0)
                      <p style="margin:10px 0 5px 0;"><strong style="color:#df2020;">Required Parts:</strong></p>
                      @foreach($inquiry->required_parts as $part)
                      <p style="margin:0 0 5px 0;">• {{ $part }}</p>
                      @endforeach
                      @endif

                      @if($inquiry->quantity)
                      <p style="margin:10px 0 5px 0;"><strong>Quantity:</strong> {{ $inquiry->quantity }}</p>
                      @endif

                      @if($inquiry->description)
                      <p style="margin:10px 0 5px 0;"><strong>Description:</strong> {{ $inquiry->description }}</p>
                      @endif
                    </td>
                  </tr>
                </table>

                <p style="margin:0 0 10px 0;"><strong>Here's what happens next:</strong></p>

                <!-- Steps -->
                <table width="100%" cellpadding="20" cellspacing="0" border="0" style="background-color:#f8f9fa; border-radius:8px; margin:20px 0;">
                  <tr>
                    <td>
                      <p style="margin:0 0 15px 0;"><strong style="color:#df2020;">Review</strong> – Our specialists will check your request against our supplier network.</p>
                      <p style="margin:0 0 15px 0;"><strong style="color:#df2020;">Match</strong> – We'll find the best options for your part(s).</p>
                      <p style="margin:0;"><strong style="color:#df2020;">Reply</strong> – You'll receive a response as soon as possible with availability, pricing, and delivery options.</p>
                    </td>
                  </tr>
                </table>

                <p style="margin:0 0 20px 0;">In the meantime, if you'd like faster service, you can also reach us directly on WhatsApp:</p>

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
                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://wa.me/971561787270" style="height:44px;v-text-anchor:middle;width:250px;" arcsize="11%" stroke="f" fillcolor="#DF2020">
                        <w:anchorlock/>
                        <center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">Contact us on WhatsApp</center>
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
                              href="https://wa.me/971561787270"
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
                              Contact us on WhatsApp
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

                <p style="margin: 0 0 20px 0">
                  We appreciate your trust in O2Mart – making car care easy, reliable, and hassle-free in the UAE.
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

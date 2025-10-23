<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Email Updated – O2Mart</title>
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
            <img src="{{ \App\Models\Setting::getSetting('logo') ?? config('app.backend_url') . '/email-assets/logo.png' }}" alt="logo" border="0" />
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
                <p style="margin:0 0 15px 0;">Hello {{ $user->name }},</p>
                <p style="margin:0 0 20px 0;">Your email address has been successfully updated to <strong>{{ $user->email }}</strong>.</p>

                <p style="margin:0 0 15px 0;">If you made this change, no further action is required.</p>
                <p style="margin:0 0 20px 0;">If you did not make this change, please contact our support team immediately at <a href="mailto:support@o2mart.net" style="color:#df2020;">support@o2mart.net</a> or call us at +971 56 178 7270.</p>

                <p style="margin:0 0 10px 0;">Best regards,<br/>The O2Mart Team<br/>
                  <a href="https://www.o2mart.net" style="color:#df2020; text-decoration:none;">www.o2mart.net</a> | +971 56 178 7270
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td bgcolor="#df2020" align="center" style="color:#ffffff; padding:15px 20px; font-size:12px; font-family:Arial, sans-serif;">
                <p style="margin:0; font-weight:bold;">Follow us:</p>
@php
$socialLinks = \App\Models\Setting::getSocialMediaLinks();
$contactDetails = \App\Models\Setting::getContactDetails();
@endphp
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:10px auto;">
                  <tr>
                    @if($socialLinks['linkedin'])
                    <td align="center" style="padding:0 5px;">
                      <a href="{{ $socialLinks['linkedin'] }}" target="_blank">
                        <img src="{{ config('app.backend_url') }}/email-assets/linkedin.png" width="24" height="24" alt="LinkedIn" border="0" style="display:block;" />
                      </a>
                    </td>
                    @endif
                    @if($socialLinks['facebook'])
                    <td align="center" style="padding:0 5px;">
                      <a href="{{ $socialLinks['facebook'] }}" target="_blank">
                        <img src="{{ config('app.backend_url') }}/email-assets/facebook.png" width="24" height="24" alt="Facebook" border="0" style="display:block;" />
                      </a>
                    </td>
                    @endif
                    @if($socialLinks['instagram'])
                    <td align="center" style="padding:0 5px;">
                      <a href="{{ $socialLinks['instagram'] }}" target="_blank">
                        <img src="{{ config('app.backend_url') }}/email-assets/instagram.png" width="24" height="24" alt="Instagram" border="0" style="display:block;" />
                      </a>
                    </td>
                    @endif
                    @if($contactDetails['phone'])
                    <td align="center" style="padding:0 5px;">
                      <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $contactDetails['phone']) }}" target="_blank">
                        <img src="{{ config('app.backend_url') }}/email-assets/whatsapp.png" width="24" height="24" alt="WhatsApp" border="0" style="display:block;" />
                      </a>
                    </td>
                    @endif
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
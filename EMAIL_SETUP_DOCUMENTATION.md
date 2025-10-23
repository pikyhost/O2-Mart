# Email Template Documentation - O2Mart

## Password Reset Email Template

### Overview

The password reset email uses a custom branded template with O2Mart logo and social media links.

---

## Template Location

**File:** `resources/views/emails/password-reset.blade.php`

---

## Assets Location

All email assets (logos and social icons) are stored in:

**Directory:** `public/photos/`

**Files:**
- `logo.png` - O2Mart logo (150x30px)
- `linkedin.png` - LinkedIn icon (24x24px)
- `FB.png` - Facebook icon (24x24px)
- `IG.png` - Instagram icon (24x24px)
- `WA.png` - WhatsApp icon (30x30px)

---

## How It Works

### 1. User Requests Password Reset

When a user requests a password reset, the system:

1. Generates a unique reset token
2. Saves the token to the `password_reset_tokens` table
3. Sends an email using `CustomResetPassword` notification

### 2. Notification Class

**File:** `app/Notifications/CustomResetPassword.php`

```php
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Password Reset – O2Mart')
        ->view('emails.password-reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
            'user' => $notifiable
        ]);
}
```

### 3. User Model Integration

**File:** `app/Models/User.php`

```php
public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPassword($token));
}
```

---

## Email Template Features

### ✅ Branding
- **O2Mart logo** with red header (#DF2020)
- **Tagline:** "Your Trusted Auto Parts Partner"

### ✅ Content
- **Clear call-to-action button** ("Reset Password")
- **Security notice** (link expires in 60 minutes)
- **Alternative text link** for users who can't click the button
- **Contact information** (website + phone number)

### ✅ Social Media Links
- LinkedIn: https://www.linkedin.com/company/o2mart/
- Facebook: https://www.facebook.com/o2mart
- Instagram: https://www.instagram.com/o2mart/
- WhatsApp: https://wa.me/971561787270

### ✅ Responsive Design
- Maximum width: 600px
- Works on all email clients (Gmail, Outlook, Apple Mail, etc.)
- Uses tables for maximum compatibility

---

## Reset Link Format

```
{{ config('app.frontend_url') }}/reset-password/{{ $token }}?email={{ $email }}
```

**Example:**
```
https://www.o2mart.net/reset-password/abc123def456?email=user@example.com
```

---

## Configuration

### Frontend URL

Set in `.env`:

```env
FRONTEND_URL=https://www.o2mart.net
```

### App URL (for images)

Set in `.env`:

```env
APP_URL=https://api.o2mart.net
```

---

## Testing the Email

### Option 1: Artisan Command

```bash
php artisan tinker

# Send test email
$user = User::find(1);
$user->sendPasswordResetNotification('test-token-123');
```

### Option 2: API Endpoint

```bash
POST /api/password/email
{
  "email": "user@example.com"
}
```

---

## Email Variables

| Variable | Type | Description |
|----------|------|-------------|
| `$token` | string | Password reset token (60 char hash) |
| `$email` | string | User's email address |
| `$user` | User | User model instance (optional) |

---

## Customization Guide

### Change Header Color

Find and replace `#DF2020` with your desired color in:
- Header background: `bgcolor="#DF2020"`
- Button background: `bgcolor="#DF2020"`
- Footer background: `bgcolor="#DF2020"`
- Text links: `color: #DF2020`

### Change Logo

Replace `public/photos/logo.png` with your logo.

**Recommended size:** 150x30px (or maintain aspect ratio)

### Change Social Icons

Replace files in `public/photos/`:
- `linkedin.png` (24x24px)
- `FB.png` (24x24px)
- `IG.png` (24x24px)
- `WA.png` (30x30px)

### Update Social Links

Edit the `<a>` tags in the footer section:

```html
<a href="https://www.linkedin.com/company/YOUR_COMPANY/" target="_blank">
<a href="https://www.facebook.com/YOUR_PAGE" target="_blank">
<a href="https://www.instagram.com/YOUR_PROFILE/" target="_blank">
<a href="https://wa.me/YOUR_PHONE" target="_blank">
```

### Update Contact Info

Find this section and update:

```html
<p style="margin: 0 0 10px 0">
  Best regards,<br />The O2Mart Team<br />
  <a href="https://www.o2mart.net" style="color: #DF2020; text-decoration: none">
    www.o2mart.net
  </a>
  | +971 56 178 7270
</p>
```

---

## Email Client Compatibility

✅ **Tested and working on:**
- Gmail (Web, iOS, Android)
- Outlook (Web, Desktop)
- Apple Mail (macOS, iOS)
- Yahoo Mail
- ProtonMail
- Thunderbird

---

## Troubleshooting

### Images Not Loading

**Problem:** Email images show as broken links

**Solution:**
1. Check `APP_URL` is set correctly in `.env`
2. Ensure images exist in `public/photos/`
3. Verify server allows external access to `/photos/` directory

### Email Not Sending

**Problem:** Password reset email not received

**Solution:**
1. Check mail configuration in `.env`
2. Verify queue is running: `php artisan queue:work`
3. Check logs: `storage/logs/laravel.log`

### Reset Link Not Working

**Problem:** Reset link returns 404 or invalid token

**Solution:**
1. Verify `FRONTEND_URL` is set correctly
2. Check token hasn't expired (60 minutes)
3. Ensure frontend route `/reset-password/:token` exists

---

## Security Notes

⚠️ **Important:**
- Reset tokens expire after 60 minutes
- Tokens are single-use only
- Tokens are hashed in the database
- Email must match the token in the database

---

## Related Files

```
app/
├── Models/
│   └── User.php                           # sendPasswordResetNotification()
├── Notifications/
│   └── CustomResetPassword.php            # Email notification class
└── Http/Controllers/Auth/
    ├── PasswordResetLinkController.php    # Send reset link
    └── NewPasswordController.php           # Handle password reset

resources/views/emails/
└── password-reset.blade.php               # Email template

public/photos/
├── logo.png
├── linkedin.png
├── FB.png
├── IG.png
└── WA.png
```

---

## Mail Configuration (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@o2mart.net
MAIL_FROM_NAME="O2Mart"
```

---

## Best Practices

✅ **DO:**
- Keep images small (< 100KB each)
- Use absolute URLs for images
- Test on multiple email clients
- Include alt text for images
- Provide text version of reset link

❌ **DON'T:**
- Use JavaScript in emails
- Embed large images inline
- Use complex CSS (many clients strip it)
- Forget to set MAIL_FROM_NAME

---

## Support

For issues with email delivery or template customization, contact:
- **Website:** https://www.o2mart.net
- **Phone:** +971 56 178 7270
- **Email:** support@o2mart.net

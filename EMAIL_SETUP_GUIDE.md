
# Email Setup Guide for Trade Author

## Option 1: Gmail SMTP (Recommended for Development)

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings
2. Enable 2-factor authentication

### Step 2: Create App Password
1. Go to Google Account > Security > 2-Step Verification
2. Scroll down to "App passwords"
3. Generate a new app password for "Mail"
4. Use this 16-character password in your SMTP configuration

### Step 3: Update Configuration
Edit `app/config/email_config.php`:
```php
'smtp_host' => 'smtp.gmail.com',
'smtp_port' => 587,
'smtp_username' => 'your-email@gmail.com',
'smtp_password' => 'your-16-character-app-password',
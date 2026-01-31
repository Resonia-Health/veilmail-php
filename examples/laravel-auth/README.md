# Laravel Auth with VeilMail

A Laravel authentication example using the VeilMail PHP SDK for transactional emails. Covers email verification, password reset, two-factor authentication, and security notifications.

## Key Files

| File | Purpose |
|------|---------|
| `app/Services/VeilMailService.php` | Centralized email service wrapping the VeilMail SDK |
| `app/Http/Controllers/AuthController.php` | Auth endpoints (register, login, verify, reset) |
| `routes/api.php` | API route definitions |
| `config/services.php` | VeilMail configuration |
| `database/migrations/create_auth_tokens_table.php` | Token tables for verification, password reset, and 2FA |

## Setup

1. Install the SDK:
   ```bash
   composer require veilmail/veilmail-php
   ```

2. Add to your `.env`:
   ```
   VEILMAIL_API_KEY=veil_live_xxx
   VEILMAIL_FROM_EMAIL=noreply@yourdomain.com
   ```

3. Copy the files into your Laravel project and run migrations:
   ```bash
   php artisan migrate
   ```

## Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register and send verification email |
| GET | `/api/auth/verify-email` | Verify email with token |
| POST | `/api/auth/login` | Login (triggers 2FA if enabled) |
| POST | `/api/auth/verify-2fa` | Complete login with 2FA code |
| POST | `/api/auth/forgot-password` | Request password reset email |
| POST | `/api/auth/reset-password` | Reset password with token |
| GET | `/api/users/me` | Get current user (auth required) |
| POST | `/api/users/toggle-2fa` | Toggle 2FA on/off (auth required) |

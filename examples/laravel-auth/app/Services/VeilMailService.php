<?php

namespace App\Services;

use VeilMail\VeilMailClient;

class VeilMailService
{
    private VeilMailClient $client;
    private string $from;
    private string $appUrl;

    public function __construct()
    {
        $this->client = new VeilMailClient(config('services.veilmail.api_key'));
        $this->from = config('services.veilmail.from_email', 'noreply@veilmail.xyz');
        $this->appUrl = config('app.url', 'http://localhost:8000');
    }

    public function sendVerificationEmail(string $email, string $name, string $token): void
    {
        $url = "{$this->appUrl}/auth/verify-email?token={$token}";
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => 'Verify your email address',
            'html' => "<p>Hi {$name},</p><p>Click <a href=\"{$url}\">here</a> to verify your email. Expires in 1 hour.</p>",
            'tags' => ['auth', 'verification'],
            'type' => 'transactional',
        ]);
    }

    public function sendPasswordResetEmail(string $email, string $token): void
    {
        $url = "{$this->appUrl}/auth/reset-password?token={$token}";
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => 'Reset your password',
            'html' => "<p>Click <a href=\"{$url}\">here</a> to reset your password. Expires in 1 hour.</p>",
            'tags' => ['auth', 'password-reset'],
            'type' => 'transactional',
        ]);
    }

    public function sendTwoFactorCode(string $email, string $code): void
    {
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => "{$code} is your verification code",
            'html' => "<p>Your code: <strong>{$code}</strong></p><p>Expires in 5 minutes.</p>",
            'tags' => ['auth', '2fa'],
            'type' => 'transactional',
        ]);
    }

    public function sendWelcomeEmail(string $email, string $name): void
    {
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => 'Welcome!',
            'html' => "<p>Welcome, {$name}! Your account is active.</p>",
            'tags' => ['auth', 'welcome'],
            'type' => 'transactional',
        ]);
    }

    public function sendPasswordChangedEmail(string $email): void
    {
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => 'Your password was changed',
            'html' => '<p>Your password was changed. If you didn\'t do this, reset it immediately.</p>',
            'tags' => ['auth', 'security'],
            'type' => 'transactional',
        ]);
    }

    public function sendTwoFactorToggledEmail(string $email, bool $enabled): void
    {
        $status = $enabled ? 'enabled' : 'disabled';
        $this->client->emails->send([
            'from' => $this->from,
            'to' => $email,
            'subject' => "Two-factor authentication {$status}",
            'html' => "<p>2FA has been {$status} on your account.</p>",
            'tags' => ['auth', '2fa', 'security'],
            'type' => 'transactional',
        ]);
    }
}

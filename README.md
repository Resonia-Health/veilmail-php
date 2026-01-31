# Veil Mail PHP SDK

Official PHP SDK for the [Veil Mail](https://veilmail.xyz) API. Send emails with built-in PII protection.

## Requirements

- PHP 8.1+
- ext-curl
- ext-json

## Installation

```bash
composer require veilmail/veilmail-php
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

$client = new VeilMail\VeilMail('veil_live_xxxxx');

$email = $client->emails->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'user@example.com',
    'subject' => 'Hello from PHP!',
    'html' => '<h1>Welcome!</h1>',
]);

echo $email['id'];     // email_xxxxx
echo $email['status']; // queued
```

## Configuration

```php
$client = new VeilMail\VeilMail(
    apiKey: 'veil_live_xxxxx',
    baseUrl: 'https://custom-api.example.com',
    timeout: 10,
);
```

## Emails

```php
// Send with named sender
$email = $client->emails->send([
    'from' => 'Alice <alice@yourdomain.com>',
    'to' => ['bob@example.com'],
    'subject' => 'Hello',
    'html' => '<p>Hello Bob!</p>',
    'tags' => ['welcome'],
]);

// Send with template
$email = $client->emails->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'user@example.com',
    'templateId' => 'tmpl_xxx',
    'templateData' => ['name' => 'Alice'],
]);

// Send with attachments
$email = $client->emails->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'user@example.com',
    'subject' => 'Invoice',
    'html' => '<p>Attached is your invoice.</p>',
    'attachments' => [
        ['filename' => 'invoice.pdf', 'content' => $base64Content, 'contentType' => 'application/pdf'],
    ],
]);

// Batch send (up to 100)
$result = $client->emails->sendBatch([
    ['from' => 'hello@yourdomain.com', 'to' => ['user1@example.com'], 'subject' => 'Hi', 'html' => '<p>Hi!</p>'],
    ['from' => 'hello@yourdomain.com', 'to' => ['user2@example.com'], 'subject' => 'Hi', 'html' => '<p>Hi!</p>'],
]);

// List, get, cancel, reschedule
$emails = $client->emails->list(['status' => 'delivered', 'limit' => 10]);
$email = $client->emails->get('email_xxx');
$result = $client->emails->cancel('email_xxx');
$email = $client->emails->update('email_xxx', ['scheduledFor' => '2025-07-01T09:00:00Z']);
```

## Domains

```php
// Add and verify a domain
$domain = $client->domains->create(['domain' => 'mail.example.com']);
$domain = $client->domains->verify($domain['id']);

// Update tracking
$domain = $client->domains->update($domain['id'], [
    'trackOpens' => true,
    'trackClicks' => true,
]);

// List and delete
$domains = $client->domains->list();
$client->domains->delete($domain['id']);
```

## Templates

```php
$template = $client->templates->create([
    'name' => 'Welcome',
    'subject' => 'Welcome, {{name}}!',
    'html' => '<h1>Hello {{name}}</h1>',
    'variables' => [
        ['name' => 'name', 'type' => 'string', 'required' => true],
    ],
]);

// Preview
$preview = $client->templates->preview([
    'html' => '<h1>Hello {{name}}</h1>',
    'variables' => ['name' => 'Alice'],
]);
```

## Audiences & Subscribers

```php
$audience = $client->audiences->create(['name' => 'Newsletter']);
$subs = $client->audiences->subscribers($audience['id']);

// Add subscriber
$subscriber = $subs->add([
    'email' => 'user@example.com',
    'firstName' => 'Alice',
    'lastName' => 'Smith',
]);

// List, import, export
$subscribers = $subs->list(['status' => 'active', 'limit' => 50]);
$result = $subs->import(['csvData' => "email,firstName\nuser@example.com,Bob"]);
$csv = $subs->export(['status' => 'active']);

// Activity timeline
$events = $subs->activity($subscriber['id'], ['limit' => 20]);
```

## Campaigns

```php
$campaign = $client->campaigns->create([
    'name' => 'Summer Sale',
    'subject' => '50% Off!',
    'from' => 'Store <deals@yourdomain.com>',
    'audienceId' => 'aud_xxx',
    'html' => '<h1>Summer Sale!</h1>',
]);

// Schedule, send, pause, resume, cancel
$client->campaigns->schedule($campaign['id'], ['scheduledAt' => '2025-06-15T10:00:00Z']);
$client->campaigns->send($campaign['id']);
$client->campaigns->pause($campaign['id']);
$client->campaigns->resume($campaign['id']);
$client->campaigns->cancel($campaign['id']);
```

## Webhooks

```php
$webhook = $client->webhooks->create([
    'url' => 'https://yourdomain.com/webhooks/veilmail',
    'events' => ['email.delivered', 'email.bounced'],
]);

// Test and rotate secret
$result = $client->webhooks->test($webhook['id']);
$webhook = $client->webhooks->rotateSecret($webhook['id']);
```

### Signature Verification

```php
// In your webhook handler
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SIGNATURE_HASH'] ?? '';

if (!VeilMail\Webhook::verifySignature($body, $signature, $webhookSecret)) {
    http_response_code(401);
    exit;
}

$event = json_decode($body, true);
switch ($event['type']) {
    case 'email.delivered':
        // Handle delivery
        break;
    case 'email.bounced':
        // Handle bounce
        break;
}

http_response_code(200);
```

### Laravel Webhook Controller

```php
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('X-Signature-Hash', '');

        if (!VeilMail\Webhook::verifySignature($body, $signature, config('services.veilmail.webhook_secret'))) {
            return response('Unauthorized', 401);
        }

        $event = $request->json()->all();

        match ($event['type']) {
            'email.delivered' => $this->handleDelivered($event),
            'email.bounced' => $this->handleBounced($event),
            default => null,
        };

        return response('OK', 200);
    }
}
```

## Topics

```php
$topic = $client->topics->create([
    'name' => 'Product Updates',
    'isDefault' => true,
]);
$topics = $client->topics->list(['active' => true]);

// Subscriber preferences
$prefs = $client->topics->getPreferences('aud_xxx', 'sub_xxx');
$client->topics->setPreferences('aud_xxx', 'sub_xxx', [
    'topics' => [
        ['topicId' => 'topic_xxx', 'subscribed' => true],
        ['topicId' => 'topic_yyy', 'subscribed' => false],
    ],
]);
```

## Contact Properties

```php
$prop = $client->properties->create([
    'key' => 'company',
    'name' => 'Company Name',
    'type' => 'text',
]);

// Set values for a subscriber
$client->properties->setValues('aud_xxx', 'sub_xxx', ['company' => 'Acme Corp']);
$values = $client->properties->getValues('aud_xxx', 'sub_xxx');
```

## Error Handling

```php
use VeilMail\Exceptions\AuthenticationException;
use VeilMail\Exceptions\ForbiddenException;
use VeilMail\Exceptions\NotFoundException;
use VeilMail\Exceptions\PiiDetectedException;
use VeilMail\Exceptions\RateLimitException;
use VeilMail\Exceptions\ValidationException;
use VeilMail\Exceptions\VeilMailException;

try {
    $client->emails->send([
        'from' => 'hello@yourdomain.com',
        'to' => 'user@example.com',
        'subject' => 'Hello',
        'html' => '<p>Hi!</p>',
    ]);
} catch (RateLimitException $e) {
    echo "Rate limited. Retry after {$e->getRetryAfter()}s";
} catch (PiiDetectedException $e) {
    echo "PII detected: " . implode(', ', $e->getPiiTypes());
} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}";
} catch (AuthenticationException $e) {
    echo "Invalid API key";
} catch (VeilMailException $e) {
    echo "API error: {$e->getMessage()} (code: {$e->getErrorCode()})";
}
```

## License

MIT

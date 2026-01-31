<?php

declare(strict_types=1);

namespace VeilMail;

/**
 * Utility for verifying webhook signatures.
 */
class Webhook
{
    /**
     * Verify a webhook signature using constant-time HMAC-SHA256 comparison.
     *
     * @param string $body      The raw request body
     * @param string $signature The signature from the X-Signature-Hash header
     * @param string $secret    The webhook signing secret
     *
     * @return bool True if the signature is valid
     *
     * @example
     * ```php
     * $body = file_get_contents('php://input');
     * $signature = $_SERVER['HTTP_X_SIGNATURE_HASH'] ?? '';
     *
     * if (!VeilMail\Webhook::verifySignature($body, $signature, $webhookSecret)) {
     *     http_response_code(401);
     *     exit;
     * }
     * ```
     */
    public static function verifySignature(string $body, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $body, $secret);

        return hash_equals($expected, $signature);
    }
}

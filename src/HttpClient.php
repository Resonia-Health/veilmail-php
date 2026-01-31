<?php

declare(strict_types=1);

namespace VeilMail;

use VeilMail\Exceptions\AuthenticationException;
use VeilMail\Exceptions\ForbiddenException;
use VeilMail\Exceptions\NotFoundException;
use VeilMail\Exceptions\PiiDetectedException;
use VeilMail\Exceptions\RateLimitException;
use VeilMail\Exceptions\ServerException;
use VeilMail\Exceptions\ValidationException;
use VeilMail\Exceptions\VeilMailException;

/**
 * Internal HTTP client for communicating with the Veil Mail API.
 *
 * @internal
 */
class HttpClient
{
    private const DEFAULT_BASE_URL = 'https://api.veilmail.xyz';
    private const DEFAULT_TIMEOUT = 30;
    private const VERSION = '0.1.0';

    private string $apiKey;
    private string $baseUrl;
    private int $timeout;

    public function __construct(string $apiKey, ?string $baseUrl = null, ?int $timeout = null)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl ?? self::DEFAULT_BASE_URL, '/');
        $this->timeout = $timeout ?? self::DEFAULT_TIMEOUT;
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function post(string $path, ?array $body = null): array
    {
        return $this->request('POST', $path, body: $body);
    }

    /**
     * @return array<string, mixed>
     */
    public function patch(string $path, ?array $body = null): array
    {
        return $this->request('PATCH', $path, body: $body);
    }

    /**
     * @return array<string, mixed>
     */
    public function put(string $path, ?array $body = null): array
    {
        return $this->request('PUT', $path, body: $body);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    /**
     * Makes a raw HTTP request and returns the response body as a string.
     * Used for non-JSON responses like CSV exports.
     */
    public function getRaw(string $path, array $query = []): string
    {
        $url = $this->buildUrl($path, $query);
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $this->buildHeaders(false),
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new VeilMailException("Network error: {$error}");
        }

        if ($statusCode >= 400) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                $this->throwApiError($statusCode, $data);
            }
            throw new VeilMailException("HTTP error {$statusCode}", statusCode: $statusCode);
        }

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, ?array $body = null, array $query = []): array
    {
        $url = $this->buildUrl($path, $query);
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $this->buildHeaders(true),
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($body !== null && in_array($method, ['POST', 'PATCH', 'PUT'], true)) {
            $filtered = $this->filterNulls($body);
            $options[CURLOPT_POSTFIELDS] = json_encode($filtered, JSON_THROW_ON_ERROR);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new VeilMailException("Network error: {$error}");
        }

        if ($statusCode === 204 || $response === '') {
            return [];
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            if ($statusCode >= 400) {
                throw new VeilMailException("HTTP error {$statusCode}", statusCode: $statusCode);
            }
            return [];
        }

        if ($statusCode >= 400) {
            $this->throwApiError($statusCode, $data);
        }

        return $data;
    }

    private function buildUrl(string $path, array $query = []): string
    {
        $url = $this->baseUrl . $path;
        $filtered = array_filter($query, fn ($v) => $v !== null);

        if (!empty($filtered)) {
            // Convert booleans to strings
            $stringified = array_map(fn ($v) => is_bool($v) ? ($v ? 'true' : 'false') : $v, $filtered);
            $url .= '?' . http_build_query($stringified);
        }

        return $url;
    }

    /**
     * @return string[]
     */
    private function buildHeaders(bool $json): array
    {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'User-Agent: veilmail-php/' . self::VERSION,
        ];

        if ($json) {
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Accept: application/json';
        }

        return $headers;
    }

    /**
     * Recursively remove null values from an array.
     */
    private function filterNulls(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }
            if (is_array($value) && !array_is_list($value)) {
                $result[$key] = $this->filterNulls($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @throws VeilMailException
     * @never-return
     */
    private function throwApiError(int $statusCode, array $data): never
    {
        $error = $data['error'] ?? $data;
        $message = $error['message'] ?? 'Unknown error';
        $code = $error['code'] ?? null;
        $details = $error['details'] ?? null;

        throw match (true) {
            $statusCode === 401 => new AuthenticationException($message, $code, $details),
            $statusCode === 403 => new ForbiddenException($message, $code, $details),
            $statusCode === 404 => new NotFoundException($message, $code, $details),
            $statusCode === 422 && ($code === 'pii_detected' || isset($error['piiTypes'])) => new PiiDetectedException(
                $message,
                $error['piiTypes'] ?? [],
                $code,
                $details,
            ),
            $statusCode === 429 => new RateLimitException(
                $message,
                isset($error['retryAfter']) ? (int) $error['retryAfter'] : null,
                $code,
                $details,
            ),
            $statusCode === 400 => new ValidationException($message, $code, $details),
            $statusCode >= 500 => new ServerException($message, $code, $statusCode, $details),
            default => new VeilMailException($message, $code, $statusCode, $details),
        };
    }
}

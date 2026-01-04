<?php

namespace LemonSqueezy\Http;

use Psr\Http\Message\ResponseInterface;
use LemonSqueezy\Exception\{
    ClientException,
    ServerException,
    NotFoundException,
    UnauthorizedException,
    RateLimitException,
    HttpException
};

/**
 * Handles HTTP responses and raises appropriate exceptions
 */
class ResponseHandler
{
    /**
     * Handle an HTTP response and raise exceptions if needed
     *
     * @throws HttpException on various error conditions
     */
    public function handle(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            // Success response
            return $this->parseResponseBody($response);
        }

        // Parse error response
        $data = $this->parseResponseBody($response);
        $message = $this->extractErrorMessage($data, $statusCode);

        // Handle specific error codes
        match ($statusCode) {
            401 => throw new UnauthorizedException($message, $statusCode, $data),
            404 => throw new NotFoundException($message, $statusCode, $data),
            429 => throw new RateLimitException(
                $message,
                $statusCode,
                $this->extractResetTime($response),
                $this->extractRemainingRequests($response),
                $data
            ),
            400, 403, 422 => throw new ClientException($message, $statusCode, $data),
            default => match (true) {
                $statusCode >= 400 && $statusCode < 500 => throw new ClientException($message, $statusCode, $data),
                $statusCode >= 500 => throw new ServerException($message, $statusCode, $data),
                default => throw new HttpException($message, $statusCode, $data),
            }
        };
    }

    /**
     * Parse response body as JSON
     */
    private function parseResponseBody(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        if (empty($body)) {
            return [];
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded) ? $decoded : ['data' => $decoded];
        } catch (\JsonException) {
            return ['raw_body' => $body];
        }
    }

    /**
     * Extract error message from response data
     */
    private function extractErrorMessage(array $data, int $statusCode): string
    {
        // JSON:API error format
        if (isset($data['errors']) && is_array($data['errors'])) {
            $first_error = $data['errors'][0] ?? null;
            if (is_array($first_error) && isset($first_error['detail'])) {
                return $first_error['detail'];
            }
            if (is_array($first_error) && isset($first_error['title'])) {
                return $first_error['title'];
            }
        }

        // Standard error message
        if (isset($data['message'])) {
            return $data['message'];
        }

        if (isset($data['error'])) {
            return is_array($data['error']) ? json_encode($data['error']) : $data['error'];
        }

        // Fallback to HTTP status text
        return match ($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            default => "HTTP {$statusCode}",
        };
    }

    /**
     * Extract rate limit reset time from response headers
     */
    private function extractResetTime(ResponseInterface $response): ?\DateTime
    {
        $resetHeader = $response->getHeaderLine('X-RateLimit-Reset');

        if (!$resetHeader) {
            return null;
        }

        try {
            // Try to parse as Unix timestamp
            if (is_numeric($resetHeader)) {
                return new \DateTime('@' . $resetHeader);
            }

            // Try to parse as datetime string
            return new \DateTime($resetHeader);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Extract remaining requests from response headers
     */
    private function extractRemainingRequests(ResponseInterface $response): int
    {
        $remaining = $response->getHeaderLine('X-RateLimit-Remaining');

        return is_numeric($remaining) ? (int)$remaining : 0;
    }
}

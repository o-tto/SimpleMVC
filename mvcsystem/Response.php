<?php

declare(strict_types=1);

namespace mvcsystem;

/**
 * An HTTP response. Controllers return one of these instead of echoing
 * output and calling die(), so the framework (and tests) stay in control.
 */
final class Response
{
    /** Headers sent with every response unless overridden. */
    public const DEFAULT_HEADERS = [
        'Content-Type' => 'text/html; charset=utf-8',
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ];

    /** @var array<string, string> */
    public readonly array $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        array $headers = [],
    ) {
        $this->headers = array_replace(self::DEFAULT_HEADERS, $headers);
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    public static function text(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public function send(): void
    {
        if (!headers_sent()) {
            header_remove('X-Powered-By');
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                // Strip CR/LF so a header value can never inject extra headers.
                header($name . ': ' . str_replace(["\r", "\n"], '', $value));
            }
        }
        echo $this->body;
    }
}

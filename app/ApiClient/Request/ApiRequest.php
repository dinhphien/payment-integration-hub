<?php

declare(strict_types=1);

namespace App\ApiClient\Request;

use App\ValueObjects\URL;

class ApiRequest implements RequestInterface
{
    public static function fromParameters(URL $url, string $method, array $headers = [], array $body = []): self
    {
        return new self($url, $method, $headers, $body);
    }

    public function __construct(
        private URL $url,
        private string $method,
        private array $headers,
        private array $body
    ) {
    }

    public function getUrl(): URL
    {
        return $this->url;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}

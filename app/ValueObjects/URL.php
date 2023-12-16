<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

class URL
{
    private string $url;

    private array $supportedSchemes = [
        'http',
        'https',
    ];

    public static function fromString(string $url): self
    {
        return new self($url);
    }

    private function __construct(string $url)
    {
        self::ensureValidURL($url);
        self::ensureSupportedScheme($url);
        $this->url = $url;
    }

    public function asString(): string
    {
        return $this->url;
    }

    private function ensureSupportedScheme(string $url): void
    {
        $parts = (array) parse_url($url);

        if (! isset($parts['scheme']) || ! in_array($parts['scheme'], $this->supportedSchemes, true)) {
            throw new InvalidArgumentException('Scheme not supported');
        }
    }

    private function ensureValidURL(string $url): void
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Argument must be an valid URL');
        }
    }
}

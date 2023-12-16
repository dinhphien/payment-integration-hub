<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

class Currency
{
    private string $currencyCode;

    private array $supported = ['USD'];

    public static function fromCurrencyCode(string $currencyCode): self
    {
        return new self($currencyCode);
    }

    public function __construct(string $currencyCode)
    {
        $this->ensureCurrencyCodeIsValid($currencyCode);
        $this->currencyCode = $currencyCode;
    }

    public function asString(): string
    {
        return $this->currencyCode;
    }

    private function ensureCurrencyCodeIsValid(string $currency): void
    {
        if (! in_array($currency, $this->supported)) {
            throw new InvalidArgumentException(
                sprintf('Currency code is not supported: %s', $currency)
            );
        }
    }
}

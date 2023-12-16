<?php

declare(strict_types=1);

namespace App\ValueObjects;

class Money
{
    public static function fromParameters(Amount $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    public function __construct(
        private Amount $amount,
        private Currency $currency
    ) {
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}

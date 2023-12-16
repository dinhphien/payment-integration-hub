<?php

declare(strict_types=1);

namespace App\ValueObjects;

class Amount
{
    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }

    public function __construct(private float $amount)
    {
    }

    public function asFloat(): float
    {
        return $this->amount;
    }
}

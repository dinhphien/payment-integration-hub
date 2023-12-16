<?php

declare(strict_types=1);

namespace App\ValueObjects;

class Email
{
    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function __construct(
        private string $email
    ) {
    }

    public function asString(): string
    {
        return $this->email;
    }
}

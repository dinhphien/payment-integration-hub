<?php

declare(strict_types=1);

namespace App\ValueObjects;

class StoreId
{
    public static function fromStringId(string $id): self
    {
        return new self($id);
    }

    public function __construct(
        private string $id
    ) {
    }

    public function asString(): string
    {
        return $this->id;
    }
}

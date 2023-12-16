<?php

declare(strict_types=1);

namespace App\Http\RequestValidators;

use Illuminate\Http\Request;

abstract class BaseRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        protected array $validatedParams = [],
        protected array $errors = []
    ) {
    }

    abstract public function validate(Request $request): bool;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidatedParams(): array
    {
        return $this->validatedParams;
    }
}

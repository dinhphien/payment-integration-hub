<?php

declare(strict_types=1);

namespace App\Http\RequestValidators;

use Illuminate\Http\Request;

interface RequestValidatorInterface
{
    public function validate(Request $request): bool;

    public function getErrors(): array;

    public function getValidatedParams(): array;
}

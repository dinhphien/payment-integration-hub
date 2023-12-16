<?php

declare(strict_types=1);

namespace App\ApiClient\Request;

use App\ValueObjects\URL;

interface RequestInterface
{
    public function getUrl(): URL;

    public function getBody(): array;

    public function getMethod(): string;

    public function getHeaders(): array;
}

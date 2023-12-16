<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Request\RequestInterface;

interface ClientInterface
{
    public function send(RequestInterface $request): array;
}

<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Request\RequestInterface;

class Client implements ClientInterface
{
    public function send(RequestInterface $request): array
    {
        // fake data
        $redirectUrl = sprintf('http://your.payment-provider.com/api/widget/%s', rand());

        return ['checkout_url' => $redirectUrl];
    }
}

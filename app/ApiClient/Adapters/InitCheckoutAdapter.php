<?php

declare(strict_types=1);

namespace App\ApiClient\Adapters;

use App\ApiClient\ClientInterface;
use App\ApiClient\RequestBuilders\InitCheckoutRequestBuilder;
use App\ApiClient\Responses\InitCheckoutResponse;
use App\ValueObjects\CheckoutId;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;

class InitCheckoutAdapter
{
    public function __construct(
        private InitCheckoutRequestBuilder $checkoutRequestBuilder,
        private ClientInterface $client
    ) {
    }

    public function getCheckoutUrl(
        CheckoutId $checkoutId,
        StoreId $storeId,
        Email $customerEmail,
        Money $grandTotal,
        URL $callBackUrl
    ): InitCheckoutResponse {
        $request = $this->checkoutRequestBuilder->build(
            $checkoutId,
            $storeId,
            $customerEmail,
            $grandTotal,
            $callBackUrl
        );

        $data = $this->client->send($request);

        return InitCheckoutResponse::fromData($data);
    }
}

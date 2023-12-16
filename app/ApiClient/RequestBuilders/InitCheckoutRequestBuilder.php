<?php

declare(strict_types=1);

namespace App\ApiClient\RequestBuilders;

use App\ApiClient\Request\ApiRequest;
use App\ValueObjects\CheckoutId;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;

class InitCheckoutRequestBuilder
{
    public function build(
        CheckoutId $checkoutId,
        StoreId $storeId,
        Email $customerEmail,
        Money $grandTotal,
        URL $callBackUrl
    ): ApiRequest {
        $url = URL::fromString(env('CHECKOUT_API_URL', ''));
        $method = 'POST';
        $headers = ['Content-Type' => 'application/json'];
        $body = [
            'merchant_store_id' => $storeId->asString(),
            'email' => $customerEmail->asString(),
            'amount' => $grandTotal->getAmount()->asFloat(),
            'currency_code' => $grandTotal->getCurrency()->asString(),
            'merchant_order_id' => $checkoutId->asString(),
            'callback_url' => $callBackUrl->asString(),
        ];

        return ApiRequest::fromParameters($url, $method, $headers, $body);
    }
}

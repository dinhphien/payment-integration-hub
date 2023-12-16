<?php

declare(strict_types=1);

namespace App\Services;

use App\ApiClient\Adapters\InitCheckoutAdapter;
use App\ValueObjects\CheckoutId;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;

class PaymentService
{
    public function __construct(
        private InitCheckoutAdapter $adapter
    ) {
    }

    public function initCheckout(
        CheckoutId $checkoutId,
        StoreId $storeId,
        Email $customerEmail,
        Money $grandTotal,
        URL $callBackUrl
    ): URL {
        $res = $this->adapter->getCheckoutUrl($checkoutId, $storeId, $customerEmail, $grandTotal, $callBackUrl);

        return $res->getCheckoutURL();
    }
}

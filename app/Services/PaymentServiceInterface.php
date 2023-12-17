<?php

declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\CheckoutId;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;

interface PaymentServiceInterface
{
    public function initCheckout(
        CheckoutId $checkoutId,
        StoreId $storeId,
        Email $customerEmail,
        Money $grandTotal,
        URL $callBackUrl
    ): URL;
}

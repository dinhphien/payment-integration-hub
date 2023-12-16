<?php

declare(strict_types=1);

namespace App\CommandHandlers\Commands;

use App\ValueObjects\CheckoutId;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;

class InitCheckoutCommand implements CommandInterface
{
    public static function fromParameters(
        CheckoutId $checkoutId,
        StoreId $storeId,
        Money $grandTotal,
        Email $email,
        URL $callback
    ): self {
        return new self($checkoutId, $storeId, $grandTotal, $email, $callback);
    }

    public function __construct(
        private CheckoutId $checkoutId,
        private StoreId $storeId,
        private Money $grandTotal,
        private Email $customerEmail,
        private URL $urlCallBack,
    ) {
    }

    public function getCheckoutId(): CheckoutId
    {
        return $this->checkoutId;
    }

    public function getStoreId(): StoreId
    {
        return $this->storeId;
    }

    public function getGrandTotal(): Money
    {
        return $this->grandTotal;
    }

    public function getCustomerEmail(): Email
    {
        return $this->customerEmail;
    }

    public function getUrlCallBack(): URL
    {
        return $this->urlCallBack;
    }
}

<?php

declare(strict_types=1);

namespace App\ApiClient\Responses;

use App\ValueObjects\URL;
use InvalidArgumentException;

class InitCheckoutResponse
{
    private array $requiredFields = ['checkout_url'];

    private URL $checkoutURL;

    public static function fromData(array $data): self
    {
        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->ensureHasRequiredFields($data);
        $this->checkoutURL = URL::fromString($data['checkout_url']);
    }

    public function getCheckoutURL(): URL
    {
        return $this->checkoutURL;
    }

    private function ensureHasRequiredFields(array $data): void
    {
        foreach ($this->requiredFields as $requiredField) {
            if (! isset($data[$requiredField])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid Response, Missing field: %s', $requiredField)
                );
            }
        }
    }
}

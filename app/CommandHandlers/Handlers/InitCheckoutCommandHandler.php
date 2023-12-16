<?php

declare(strict_types=1);

namespace App\CommandHandlers\Handlers;

use App\CommandHandlers\Commands\CommandInterface;
use App\CommandHandlers\Commands\InitCheckoutCommand;
use App\Models\Checkout;
use App\Models\Request;
use App\Services\PaymentService;
use App\ValueObjects\URL;

class InitCheckoutCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function execute(CommandInterface $command): URL
    {
        assert($command instanceof InitCheckoutCommand);

        $request = Request::where('checkoutId', $command->getCheckoutId()->asString())->first();
        if (is_null($request)) {
            Request::create([
                'checkoutId' => $command->getCheckoutId()->asString(),
                'storeId' => $command->getStoreId()->asString(),
                'emailCustomer' => $command->getCustomerEmail()->asString(),
                'amount' => $command->getGrandTotal()->getAmount()->asFloat(),
                'currency' => $command->getGrandTotal()->getCurrency()->asString(),
                'callBackUrl' => $command->getUrlCallBack()->asString(),
            ]);
        }

        $checkout = Checkout::where('checkoutId', $command->getCheckoutId()->asString())->first();
        if (! is_null($checkout)) {

            return URL::fromString($checkout->checkoutUrl);
        }
        // call API to get checkout URL
        $url = $this->paymentService->initCheckout(
            $command->getCheckoutId(),
            $command->getStoreId(),
            $command->getCustomerEmail(),
            $command->getGrandTotal(),
            $command->getUrlCallBack()
        );

        Checkout::create([
            'checkoutId' => $command->getCheckoutId()->asString(),
            'checkoutUrl' => $url->asString(),
        ]);

        return $url;
    }
}

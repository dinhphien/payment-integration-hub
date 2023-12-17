<?php

namespace App\Http\Controllers\V1;

use App\CommandHandlers\Commands\InitCheckoutCommand;
use App\CommandHandlers\Handlers\InitCheckoutCommandHandler;
use App\Http\Controllers\Controller;
use App\Http\RequestValidators\Cafe24CheckoutRequestValidator;
use App\ValueObjects\Amount;
use App\ValueObjects\CheckoutId;
use App\ValueObjects\Currency;
use App\ValueObjects\Email;
use App\ValueObjects\Money;
use App\ValueObjects\StoreId;
use App\ValueObjects\URL;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class Cafe24CheckoutController extends Controller
{
    public function __construct(
        private Cafe24CheckoutRequestValidator $requestValidator,
        private InitCheckoutCommandHandler $checkoutCommandHandler
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->requestValidator->validate($request)) {
            return new JsonResponse(
                ['success' => false, 'errors' => $this->requestValidator->getErrors()], 400
            );
        }
        $validatedData = $this->requestValidator->getValidatedParams();

        try {
            $command = InitCheckoutCommand::fromParameters(
                CheckoutId::fromStringId($validatedData['cafe24_checkout_id']),
                StoreId::fromStringId($validatedData['cafe24_store_id']),
                Money::fromParameters(
                    Amount::fromFloat($validatedData['cafe24_amount']),
                    Currency::fromCurrencyCode($validatedData['cafe24_currency_code'])
                ),
                Email::fromString($validatedData['cafe24_customer_email']),
                URL::fromString($validatedData['cafe24_payment_callback_url'])
            );

            $url = $this->checkoutCommandHandler->execute($command);

            return new JsonResponse(['success' => true, 'redirectUrl' => $url->asString()]);
        } catch (Throwable $throwable) {

            return new JsonResponse(['success' => false, 'message' => $throwable->getMessage()], 500);
        }
    }
}

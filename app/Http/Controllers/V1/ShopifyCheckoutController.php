<?php

namespace App\Http\Controllers\V1;

use App\CommandHandlers\Commands\InitCheckoutCommand;
use App\CommandHandlers\Handlers\InitCheckoutCommandHandler;
use App\Http\Controllers\Controller;
use App\Http\RequestValidators\ShopifyCheckoutRequestValidator;
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

class ShopifyCheckoutController extends Controller
{
    public function __construct(
        private ShopifyCheckoutRequestValidator $requestValidator,
        private InitCheckoutCommandHandler $checkoutCommandHandler
    ) {
    }

    /**
     * Handle the incoming request.
     */
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
                CheckoutId::fromStringId($validatedData['x_checkout_id']),
                StoreId::fromStringId($validatedData['x_shop_id']),
                Money::fromParameters(
                    Amount::fromFloat($validatedData['x_amount']),
                    Currency::fromCurrencyCode($validatedData['x_currency'])
                ),
                Email::fromString($validatedData['x_customer_email']),
                URL::fromString($validatedData['x_url_callback'])
            );

            $url = $this->checkoutCommandHandler->execute($command);

            return new JsonResponse(['success' => true, 'redirectUrl' => $url->asString()]);
        } catch (Throwable $throwable) {

            return new JsonResponse(['success' => false, 'message' => $throwable->getMessage()], 500);
        }
    }
}

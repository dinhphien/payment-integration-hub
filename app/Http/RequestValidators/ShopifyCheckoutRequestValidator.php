<?php

declare(strict_types=1);

namespace App\Http\RequestValidators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopifyCheckoutRequestValidator extends BaseRequestValidator
{
    public function validate(Request $request): bool
    {
        $rules = [
            'x_checkout_id' => 'required',
            'x_shop_id' => 'required',
            'x_amount' => "required|regex:/^\d*(\.\d{2})?$/",
            'x_currency' => 'required',
            'x_customer_email' => 'required|email',
            'x_url_callback' => 'required',
            'x_signature' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();

            return false;
        }

        $data = $validator->validated();
        if (! $this->verifySignature($data)) {
            $this->errors = [
                'x_signature' => ['The x signature field is invalid.'],
            ];

            return false;
        }

        $this->validatedParams = $data;

        return true;
    }

    private function verifySignature(array $data): bool
    {
        $signature = $data['x_signature'];
        $key = env('SECRET_KEY', 'SECRET_KEY');
        $encryptingText = sprintf(
            'x_checkout_id=%sx_shop_id=%sx_amount=%sx_currency=%s%s',
            $data['x_checkout_id'],
            $data['x_shop_id'],
            $data['x_amount'],
            $data['x_currency'],
            $key
        );

        return $signature === hash('sha256', $encryptingText);
    }
}

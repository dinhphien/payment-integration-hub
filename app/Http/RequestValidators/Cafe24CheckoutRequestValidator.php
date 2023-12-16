<?php

declare(strict_types=1);

namespace App\Http\RequestValidators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Cafe24CheckoutRequestValidator extends BaseRequestValidator
{
    public function validate(Request $request): bool
    {
        $rules = [
            'cafe24_checkout_id' => 'required',
            'cafe24_store_id' => 'required',
            'cafe24_amount' => "required|regex:/^\d*(\.\d{2})?$/",
            'cafe24_currency_code' => 'required',
            'cafe24_customer_email' => 'required|email',
            'cafe24_payment_callback_url' => 'required',
            'cafe24_hash_data' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();

            return false;
        }

        $data = $validator->validated();
        $plainText = sprintf(
            '%s%s%s%s',
            $data['cafe24_amount'],
            $data['cafe24_currency_code'],
            $data['cafe24_checkout_id'],
            $data['cafe24_store_id'],
        );
        if (! $this->verifyHashData($data['cafe24_hash_data'], $plainText)) {
            $this->errors = [
                'cafe24_hash_data' => ['The cafe24 hash data field is invalid.'],
            ];

            return false;
        }

        $this->validatedParams = $data;

        return true;
    }

    private function verifyHashData(string $hashData, string $plainText): bool
    {
        $key = env('SERVICE_KEY', 'SERVICE_KEY');

        return $hashData === base64_encode(hash_hmac('sha256', $plainText, $key, true));
    }
}

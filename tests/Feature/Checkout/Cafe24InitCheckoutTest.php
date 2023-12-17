<?php declare(strict_types=1);

namespace Tests\Feature\Checkout;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Cafe24InitCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = '/api/v1/checkout/cafe24';
    }

    public function test_get_checkout_url_successfully(): void
    {
        $params = $this->provideValidCheckoutParams();
        $res = $this->postJson($this->uri, $params);

        $res->assertStatus(200);
        $res->assertSeeText(['success', 'redirectUrl']);

        $jsonRes = json_decode($res->getContent(), true);

        $expectedRequestData = [
            "checkoutId" => "234akzvlk34074166",
            "storeId" => "2000",
            "amount" => 9.99,
            "currency" => "USD",
            "emailCustomer" => "customer1@cf24.com",
            "callBackUrl" => "https://api.cafe24.com/payment-callback/v1/oid/234akzvlk34074166"
        ];

        $expectedCheckoutData = [
            "checkoutId" => "234akzvlk34074166",
            "checkoutUrl" => $jsonRes['redirectUrl']
        ];

        $this->assertDatabaseHas('requests', $expectedRequestData);
        $this->assertDatabaseHas('checkouts', $expectedCheckoutData);
    }

    public function test_get_the_same_checkout_url_when_sending_duplicate_requests(): void
    {
        $params = $this->provideValidCheckoutParams();
        $res1 = $this->postJson($this->uri, $params);

        $res2 = $this->postJson($this->uri, $params);

        $res1->assertStatus(200);
        $res1->assertSeeText(['success', 'redirectUrl']);

        $res2->assertStatus(200);
        $res2->assertSeeText(['success', 'redirectUrl']);

        $jsonRes1 = json_decode($res1->getContent(), true);
        $jsonRes2 = json_decode($res2->getContent(), true);
        self::assertSame($jsonRes1, $jsonRes2);

        $this->assertDatabaseCount('requests', 1);
        $this->assertDatabaseCount('checkouts', 1);
    }

    private function provideValidCheckoutParams(): array
    {
        return [
            "cafe24_checkout_id" => "234akzvlk34074166",
            "cafe24_store_id" => "2000",
            "cafe24_amount" => 9.99,
            "cafe24_currency_code" => "USD",
            "cafe24_customer_email" => "customer1@cf24.com",
            "cafe24_payment_callback_url" => "https://api.cafe24.com/payment-callback/v1/oid/234akzvlk34074166",
            "cafe24_hash_data" => "5/8x84OPGouIuBoorixtF4kGLD9byfQPCvOLuhwPrMw="
        ];
    }

    /** @dataProvider provideInvalidCheckoutRequestParams */
    public function test_return_error_when_sending_invalid_requests(array $requestParams, array $expectedError): void
    {
        $res = $this->postJson($this->uri, $requestParams);

        $res->assertStatus(400);
        $res->assertJson(['success' => false, 'errors' => $expectedError]);
    }

    public static function provideInvalidCheckoutRequestParams(): array
    {
        return [
            "missing checkout id field" => [
                [
                    "cafe24_store_id" => "2000",
                    "cafe24_amount" => 9.99,
                    "cafe24_currency_code" => "USD",
                    "cafe24_customer_email" => "customer1@cf24.com",
                    "cafe24_payment_callback_url" => "https://api.cafe24.com/payment-callback/v1/oid/234akzvlk34074166",
                    "cafe24_hash_data" => "5/8x84OPGouIuBoorixtF4kGLD9byfQPCvOLuhwPrMw="
                ],
                [
                    "cafe24_checkout_id" => [
                        "The cafe24 checkout id field is required."
                    ]
                ]
            ],
            "hash data is invalid" => [
                [
                    "cafe24_checkout_id" => "234akzvlk34074166",
                    "cafe24_store_id" => "2000",
                    "cafe24_amount" => 9.99,
                    "cafe24_currency_code" => "USD",
                    "cafe24_customer_email" => "customer1@cf24.com",
                    "cafe24_payment_callback_url" => "https://api.cafe24.com/payment-callback/v1/oid/234akzvlk34074166",
                    "cafe24_hash_data" => "5/8x84OPGouIuBoorixtF4kGLD9byfQPCvOLuhwPrMw=123"
                ],
                [
                    "cafe24_hash_data" => [
                        "The cafe24 hash data field is invalid."
                    ]
                ]
            ]
        ];
    }

}

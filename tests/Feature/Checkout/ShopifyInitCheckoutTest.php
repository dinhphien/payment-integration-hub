<?php declare(strict_types=1);

namespace Tests\Feature\Checkout;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopifyInitCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = '/api/v1/checkout/shopify';
    }

    public function test_get_checkout_url_successfully(): void
    {
        $params = $this->provideValidCheckoutParams();
        $res = $this->postJson($this->uri, $params);

        $res->assertStatus(200);
        $res->assertSeeText(['success', 'redirectUrl']);

        $jsonRes = json_decode($res->getContent(), true);

        $expectedRequestData = [
            "checkoutId" => "132423409546723",
            "storeId" => "3000",
            "amount" => 9.99,
            "currency" => "USD",
            "emailCustomer" => "customer1@shopify.com",
            "callBackUrl" => "https://api.shopify.com/notify/rf/132423409546723"
        ];

        $expectedCheckoutData = [
            "checkoutId" => "132423409546723",
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
            "x_checkout_id" => "132423409546723",
            "x_shop_id" => "3000",
            "x_amount" => 9.99,
            "x_currency" => "USD",
            "x_customer_email" => "customer1@shopify.com",
            "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
            "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426"
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
                    "x_shop_id" => "3000",
                    "x_amount" => 9.99,
                    "x_currency" => "USD",
                    "x_customer_email" => "customer1@shopify.com",
                    "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
                    "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426"
                ],
                [
                    "x_checkout_id" => [
                        "The x checkout id field is required."
                    ]
                ]
            ],
            "signature data is invalid" => [
                [
                    "x_checkout_id" => "132423409546723",
                    "x_shop_id" => "3000",
                    "x_amount" => 9.99,
                    "x_currency" => "USD",
                    "x_customer_email" => "customer1@shopify.com",
                    "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
                    "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426123"
                ],
                [
                    "x_signature" => [
                        "The x signature field is invalid."
                    ]
                ]
            ]
        ];
    }

}

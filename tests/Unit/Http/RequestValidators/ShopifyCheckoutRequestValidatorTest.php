<?php declare(strict_types=1);

namespace Tests\Unit\Http\RequestValidators;

use App\Http\RequestValidators\ShopifyCheckoutRequestValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Tests\TestCase;

/** @covers \App\Http\RequestValidators\ShopifyCheckoutRequestValidator */
class ShopifyCheckoutRequestValidatorTest extends TestCase
{
    private ShopifyCheckoutRequestValidator $subject;

    private Request $request;
    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ShopifyCheckoutRequestValidator();
        $this->request = $this->createMock(Request::class);
    }

    public function test_can_validate_a_valid_request(): void
    {
        $requestData = [
            "x_checkout_id" => "132423409546723",
            "x_shop_id" => "3000",
            "x_amount" => 9.99,
            "x_currency" => "USD",
            "x_customer_email" => "customer1@shopify.com",
            "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
            "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426"
        ];

        $this->request->expects($this->once())
            ->method('all')
            ->willReturn($requestData);

        $result = $this->subject->validate($this->request);

        self::assertTrue($result);
        self::assertEmpty($this->subject->getErrors());
        self::assertSame($requestData, $this->subject->getValidatedParams());
    }

    /** @dataProvider provideInvalidRequestData */
    public function test_can_validate_required_fields(array $requestParams, array $errors): void
    {
        $this->request->expects($this->once())
            ->method('all')
            ->willReturn($requestParams);

        $result = $this->subject->validate($this->request);

        self::assertFalse($result);
        self::assertEmpty($this->subject->getValidatedParams());
        self::assertSame($errors, $this->subject->getErrors());
    }

    public function test_can_validate_signature_data(): void
    {
        $this->request->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $validator = $this->createMock(Validator::class);
        \Illuminate\Support\Facades\Validator::shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator->expects($this->once())
            ->method('fails')
            ->willReturn(false);

        $validatedData = [
            "x_checkout_id" => "132423409546723",
            "x_shop_id" => "3000",
            "x_amount" => 9.99,
            "x_currency" => "USD",
            "x_customer_email" => "customer1@shopify.com",
            "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
            "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b4714261234"
        ];
        $validator->expects($this->once())
            ->method('validated')
            ->willReturn($validatedData);

        $result = $this->subject->validate($this->request);

        self::assertFalse($result);
        self::assertEmpty($this->subject->getValidatedParams());
        self::assertSame(
            [
                'x_signature' => ['The x signature field is invalid.'],
            ],
            $this->subject->getErrors()
        );
    }

    public static function provideInvalidRequestData(): array
    {
        return [
            "missing x checkout id" => [
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
            "missing x checkout id and shop id" => [
                [
                    "x_amount" => 9.99,
                    "x_currency" => "USD",
                    "x_customer_email" => "customer1@shopify.com",
                    "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
                    "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426"
                ],
                [
                    "x_checkout_id" => [
                        "The x checkout id field is required."
                    ],
                    "x_shop_id" => [
                        "The x shop id field is required."
                    ]
                ]
            ],
            "missing amount and currency and customer email" => [
                [
                    "x_checkout_id" => "132423409546723",
                    "x_shop_id" => "3000",
                    "x_url_callback" => "https://api.shopify.com/notify/rf/132423409546723",
                    "x_signature" => "1fc795838fee12a7b15a4ddd022e8adbb21cefeccfb5076399cf8dd30b471426"
                ],
                [
                    "x_amount" => [
                        "The x amount field is required."
                    ],
                    "x_currency" => [
                        "The x currency field is required."
                    ],
                    "x_customer_email" => [
                        "The x customer email field is required."
                    ]
                ]
            ]
        ];
    }
}

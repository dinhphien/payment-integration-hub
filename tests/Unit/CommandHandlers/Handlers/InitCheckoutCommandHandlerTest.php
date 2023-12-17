<?php declare(strict_types=1);

namespace Tests\Unit\CommandHandlers\Handlers;

use App\CommandHandlers\Commands\InitCheckoutCommand;
use App\CommandHandlers\Handlers\InitCheckoutCommandHandler;
use App\Models\Checkout;
use App\Models\Request;
use App\Services\PaymentServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/** @covers \App\CommandHandlers\Handlers\InitCheckoutCommandHandler */
/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class InitCheckoutCommandHandlerTest extends TestCase
{
    use RefreshDatabase;
    private InitCheckoutCommandHandler $subject;
    private PaymentServiceInterface $paymentService;
    private Checkout|MockInterface $checkoutModel;
    private Request|MockInterface $requestModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = $this->createMock(PaymentServiceInterface::class);
        $this->subject = new InitCheckoutCommandHandler($this->paymentService);

        $this->checkoutModel = $this->mock('alias:\App\Models\Checkout');
        $this->requestModel = $this->mock('alias:\App\Models\Request');

    }

    public function test_can_init_checkout_with_a_new_request(): void
    {
        $command = $this->createMock(InitCheckoutCommand::class);

        $this->requestModel->shouldReceive('where->first')
            ->once()
            ->andReturn(null);

        $this->requestModel->shouldReceive('create')
            ->once();

        $this->checkoutModel->shouldReceive('where->first')
            ->once()
            ->andReturn(null);

        $this->paymentService->expects($this->once())
            ->method('initCheckout');

        $this->checkoutModel->shouldReceive('create')
            ->once();

        $this->subject->execute($command);
    }

    public function test_can_skip_sending_request_if_request_is_duplicated(): void
    {
        $command = $this->createMock(InitCheckoutCommand::class);

        $this->requestModel->shouldReceive('where->first')
            ->once()
            ->andReturn(new Request());

        $this->requestModel->shouldReceive('create')
            ->never();

        $checkoutInstance = new Checkout();
        $checkoutInstance->checkoutUrl = 'https://example.com';
        $this->checkoutModel->shouldReceive('where->first')
            ->once()
            ->andReturn($checkoutInstance);

        $this->paymentService->expects($this->never())
            ->method('initCheckout');

        $this->checkoutModel->shouldReceive('create')
            ->never();

        $this->subject->execute($command);
    }
}

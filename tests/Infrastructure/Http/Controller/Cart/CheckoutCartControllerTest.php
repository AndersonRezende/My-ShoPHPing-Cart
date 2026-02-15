<?php declare(strict_types=1);

namespace Infrastructure\Http\Controller\Cart;

use InvalidArgumentException;
use LogicException;
use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\CheckoutCartController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\ShowCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class CheckoutCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowLogicExceptionWhenTryingToCheckoutACartAlreadyFinished(): void {
        $useCase = $this->createMock(CheckoutCartUseCase::class);
        $useCase->method('execute')->willThrowException(new LogicException('Only opened carts can be completed'));
        $controller = new CheckoutCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->willReturn('1');
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'already-checkout-cart-id']);

        $this->assertEquals(404, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldCheckoutOpenedCart(): void {
        $useCase = $this->createMock(CheckoutCartUseCase::class);
        $useCase->method('execute')->willReturn(new CartBuilder()->withStatus(CartStatus::COMPLETED)->build());
        $controller = new CheckoutCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['userId' => '1']));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->willReturn('1');
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'cart-id']);
        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(CartStatus::COMPLETED->value, $data['status']);
    }


}

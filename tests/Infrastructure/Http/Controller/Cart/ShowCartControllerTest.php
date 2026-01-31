<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Cart;

use InvalidArgumentException;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\ShowCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class ShowCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowInvalidArgumentExceptionWhenCartIdIsInvalid(): void {
        $useCase = $this->createMock(ShowCartUseCase::class);
        $useCase->method('execute')->willThrowException(new InvalidArgumentException("Cart not found"));
        $controller = new ShowCartController($useCase);
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $controller($request, new Response(), ['id' => 'invalid-cart-id']);

        $this->assertEquals(404, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnCartDataWhenCartExists(): void {
        $cart = $this->createMock(Cart::class);
        $cart->method('id')->willReturn('valid-cart-id');
        $cart->method('status')->willReturn(CartStatus::OPENED);
        $cart->method('items')->willReturn([]);
        $useCase = $this->createMock(ShowCartUseCase::class);
        $useCase->method('execute')->willReturn($cart);
        $controller = new ShowCartController($useCase);
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $controller($request, new Response(), ['id' => 'valid-cart-id']);

        $response->getBody()->rewind();
        $bodyContent = $response->getBody()->getContents();
        $expectedPayload = json_encode([
            'cart' => [
                'id' => 'valid-cart-id',
                'status' => CartStatus::OPENED,
                'items' => []
                ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedPayload, $bodyContent);
    }    
}

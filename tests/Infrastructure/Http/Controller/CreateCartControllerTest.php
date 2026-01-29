<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller;

use MyShoppingCart\Application\UseCase\CreateCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Infrastructure\Http\Controller\CreateCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class CreateCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnCreatedCartId(): void {
        $useCase = $this->createMock(CreateCartUseCase::class);
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('id')->willReturn('cart-123');
        
        $useCase->method('execute')->willReturn($cartMock);

        $controller = new CreateCartController($useCase);
        $request = $this->createMock(ServerRequestInterface::class);
        
        $response = $controller($request, new Response());
        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('cart-123', $data['id']);
    }
}

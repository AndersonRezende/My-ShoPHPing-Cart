<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\UseCase\Cart\CreateCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\CreateCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class CreateCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnCreatedCartId(): void {
        $useCase = $this->createMock(CreateCartUseCase::class);
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('id')->willReturn('cart-123');
        $useCase->method('execute')->willReturn($cartMock);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['userId' => '1']));
        $controller = new CreateCartController($useCase);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->willReturn('1');
        $request->method('getBody')->willReturn($stream);
        
        $response = $controller($request, new Response());
        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('cart-123', $data['id']);
    }
}

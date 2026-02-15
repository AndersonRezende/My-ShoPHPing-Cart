<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\CartOutput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\AddItemToCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class AddItemToCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldAddItemSuccessfully(): void {
        $useCase = $this->createMock(AddItemToCartUseCase::class);
        $useCase->expects($this->once())->method('execute')->willReturn(new CartOutput(1, [new Product('1', 'Arroz')]));

        $controller = new AddItemToCartController($useCase);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([
            'product_id' => 'prod-1',
            'quantity' => 2,
            'unit_price' => 100,
            'description' => 'Item Teste',
            'userId' => '1'
        ]));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'cart-1']);

        $this->assertEquals(201, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldNotAddItemSuccessfully(): void {
        $useCase = $this->createMock(AddItemToCartUseCase::class);
        $controller = new AddItemToCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([
            'product_id' => '',
            'quantity' => 2,
            'unit_price' => 100,
            'description' => 'Item Teste'
        ]));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'cart-1']);

        $this->assertEquals(400, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldNotAddItemSuccessfullyWhenUseCaseThrowsException(): void {
        $useCase = $this->createMock(AddItemToCartUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new DomainException('Access denied: You can not modify this cart.'));
        $controller = new AddItemToCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([
            'product_id' => 'p-1',
            'quantity' => 2,
            'unit_price' => 100,
            'description' => 'Item Teste',
            'userId' => '1'
        ]));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'cart-1']);

        $this->assertEquals(403, $response->getStatusCode());
    }
}

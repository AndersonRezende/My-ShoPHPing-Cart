<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\DTO\CartOutput;
use MyShoppingCart\Application\UseCase\AddItemToCart;
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
        $useCase = $this->createMock(AddItemToCart::class);
        $useCase->expects($this->once())->method('execute')->willReturn(new CartOutput(1, [new Product('1', 'Arroz')]));

        $controller = new AddItemToCartController($useCase);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([
            'product_id' => 'prod-1',
            'quantity' => 2,
            'unit_price' => 100,
            'description' => 'Item Teste'
        ]));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => 'cart-1']);

        $this->assertEquals(201, $response->getStatusCode());
    }
}

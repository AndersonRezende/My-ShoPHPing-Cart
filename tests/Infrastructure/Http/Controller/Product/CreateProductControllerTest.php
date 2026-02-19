<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Product\CreateProductController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class CreateProductControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnJsonWithProductWhenCreateNewProduct(): void {
        $createProductUseCase = $this->createMock(CreateProductUseCase::class);
        $createProductUseCase->expects($this->once())
            ->method('execute')
            ->willReturn(new Product('1', 'Arroz', '1'));
        $controller = new CreateProductController($createProductUseCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn(json_encode(['name' => 'Arroz', 'category_id' => '1']));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);
        $response = new Response();

        $newResponse = $controller($request, $response);
        $bodyContent = (string) $newResponse->getBody();
        $data = json_decode($bodyContent, true);
        
        $this->assertEquals(201, $newResponse->getStatusCode());
        $this->assertEquals(['application/json'], $newResponse->getHeader('Content-Type'));
        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('Arroz', $data['name']);
        $this->assertEquals('1', $data['category_id']);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnBadRequestWhenNameIsMissing(): void {
        $createProductUseCase = $this->createMock(CreateProductUseCase::class);
        $controller = new CreateProductController($createProductUseCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([]));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response());

        $this->assertEquals(400, $response->getStatusCode());
    }
}

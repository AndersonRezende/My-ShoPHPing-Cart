<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Product\UpdateProductController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class UpdateProductControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnUpdatedProductWhenSuccess(): void {
        $useCase = $this->createMock(UpdateProductUseCase::class);
        $useCase->method('execute')->willReturn(new Product('1', 'Arroz Integral'));
        $controller = new UpdateProductController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['name' => 'Arroz Integral']));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);
        $response = new Response();
        $args = ['id' => '1'];

        $newResponse = $controller($request, $response, $args);
        $data = json_decode((string) $newResponse->getBody(), true);
        
        $this->assertEquals(200, $newResponse->getStatusCode());
        $this->assertEquals('Arroz Integral', $data['name']);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnNotFoundWhenProductDoesNotExist(): void {
        $useCase = $this->createMock(UpdateProductUseCase::class);
        $useCase->method('execute')->willThrowException(new \RuntimeException('Product not found'));

        $controller = new UpdateProductController($useCase);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['name' => 'Ghost']));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => '999']);

        $this->assertEquals(404, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnBadRequestWhenNameIsMissing(): void {
        $controller = new UpdateProductController($this->createMock(UpdateProductUseCase::class));
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([]));
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => '1']);

        $this->assertEquals(400, $response->getStatusCode());
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\UseCase\ShowProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Product\ShowProductController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class ShowProductControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnUpdatedProductWhenSuccess(): void {
        $useCase = $this->createMock(ShowProductUseCase::class);
        $useCase->method('execute')->willReturn(new Product('1', 'Arroz Integral'));

        $controller = new ShowProductController($useCase);

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
        $useCase = $this->createMock(ShowProductUseCase::class);
        $useCase->method('execute')->willThrowException(new \RuntimeException('Product not found'));

        $controller = new ShowProductController($useCase);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['name' => 'Ghost']));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => '999']);

        $this->assertEquals(404, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnBadRequestWhenIdIsMissing(): void {
        $controller = new ShowProductController($this->createMock(ShowProductUseCase::class));
        
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode([])); // Body vazio

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), []);

        $this->assertEquals(400, $response->getStatusCode());
    }
}

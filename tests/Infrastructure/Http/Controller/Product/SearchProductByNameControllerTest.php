<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Product;

use DomainException;
use Exception;
use MyShoppingCart\Application\UseCase\Product\ListProductUseCase;
use MyShoppingCart\Application\UseCase\Product\SearchProductByNameUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Product\ListProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\SearchProductByNameController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class SearchProductByNameControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnJsonListWhenProductsExist(): void {
        $searchProductByNameUseCase = $this->createMock(SearchProductByNameUseCase::class);
        $searchProductByNameUseCase->expects($this->once())
            ->method('execute')
            ->willReturn([
                new Product('1', 'Macarrão Fettuccine'),
                new Product('2', 'Macarrão Penne'),
            ]);

        $controller = new SearchProductByNameController($searchProductByNameUseCase);
        $request = $this->createMock(ServerRequestInterface::class);

        $response = new Response();
        $args = ['name' => 'Macarrão'];

        $newResponse = $controller($request, $response, $args);
        $bodyContent = (string) $newResponse->getBody();
        $data = json_decode($bodyContent, true);

        $this->assertEquals(200, $newResponse->getStatusCode());
        $this->assertEquals(['application/json'], $newResponse->getHeader('Content-Type'));
        $this->assertCount(2, $data);
        $this->assertEquals(['id' => '1', 'name' => 'Macarrão Fettuccine'], $data[0]);
        $this->assertEquals(['id' => '2', 'name' => 'Macarrão Penne'], $data[1]);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnNotFoundWhenProductsDoesNotExist(): void {
        $useCase = $this->createMock(SearchProductByNameUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new Exception('Product not found'));
        $controller = new SearchProductByNameController($useCase);
        $request = $this->createMock(ServerRequestInterface::class);
        $args = ['name' => 'Macarrão'];

       $response = $controller($request, new Response(), $args);

        $this->assertEquals(404, $response->getStatusCode());
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\UseCase\ListProductsUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Http\Controller\Product\ListProductsController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class ListProductsControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldReturnJsonListWhenProductsExist(): void {
        $listProductsUseCase = $this->createMock(ListProductsUseCase::class);
        $listProductsUseCase->expects($this->once())
            ->method('execute')
            ->willReturn([
                new Product('1', 'Arroz'),
                new Product('2', 'Feijão'),
            ]);

        $controller = new ListProductsController($listProductsUseCase);
        $request = $this->createMock(ServerRequestInterface::class);
        
        $response = new Response();

        $newResponse = $controller($request, $response);
        $bodyContent = (string) $newResponse->getBody();
        $data = json_decode($bodyContent, true);
        
        $this->assertEquals(200, $newResponse->getStatusCode());
        $this->assertEquals(['application/json'], $newResponse->getHeader('Content-Type'));
        $this->assertCount(2, $data);
        $this->assertEquals(['id' => '1', 'name' => 'Arroz'], $data[0]);
        $this->assertEquals(['id' => '2', 'name' => 'Feijão'], $data[1]);
    }
}

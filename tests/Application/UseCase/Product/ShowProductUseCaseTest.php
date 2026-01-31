<?php declare(strict_types=1);

namespace Application\UseCase\Product;

use MyShoppingCart\Application\DTO\ShowProductInput;
use MyShoppingCart\Application\UseCase\Product\ShowProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class ShowProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteShowProduct(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $showProductUseCase = new ShowProductUseCase($productRepository);
        $showProductUseCaseInput = new ShowProductInput('1');

        $product = $showProductUseCase->execute($showProductUseCaseInput);

        $this->assertEquals('1', $product->id());
        $this->assertEquals('Pasta', $product->name());
        $this->assertSame($product::class, Product::class);
    }

    public function testShouldThrowExceptionWhenProductNotFound(): void {
        $productRepository = new ProductRepositoryPdo($this->connection);
        $showProductUseCase = new ShowProductUseCase($productRepository);
        $showProductUseCaseInput = new ShowProductInput('abc');

        $this->expectException(\RuntimeException::class);
        $showProductUseCase->execute($showProductUseCaseInput);
    }
}
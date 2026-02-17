<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Product;

use LogicException;
use MyShoppingCart\Application\DTO\SearchProductByNameInput;
use MyShoppingCart\Application\UseCase\Product\SearchProductByNameUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class SearchProductByNameUseCaseTest extends DatabaseTestCase {

    public function testExecuteShouldReturnExpectedProductsWhenSearchByName(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (2, 'Papaya')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (3, 'Potato')");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $listProductsUseCase = new SearchProductByNameUseCase($productRepository);

        $products = $listProductsUseCase->execute(new SearchProductByNameInput('pa'));

        $this->assertCount(2, $products);
        $this->assertEquals('1', $products[0]->id());
        $this->assertEquals('Pasta', $products[0]->name());
        $this->assertEquals('2', $products[1]->id());
        $this->assertEquals('Papaya', $products[1]->name());
        $this->assertSame($products[0]::class, Product::class);
        $this->assertSame($products[1]::class, Product::class);
    }

    public function testExecuteShouldThrowExceptionWhenProductNotFound(): void {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Product not found');

        $productRepository = new ProductRepositoryPdo($this->connection);
        $listProductsUseCase = new SearchProductByNameUseCase($productRepository);

        $products = $listProductsUseCase->execute(new SearchProductByNameInput('pa'));
    }

}

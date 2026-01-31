<?php declare(strict_types=1);

namespace Application\UseCase\Product;

use MyShoppingCart\Application\UseCase\Product\ListProductsUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class ListProductsUseCaseTest extends DatabaseTestCase {

    public function testExecuteListProducts(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (2, 'Papaya')");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $listProductsUseCase = new ListProductsUseCase($productRepository);
        
        $products = $listProductsUseCase->execute();

        $this->assertCount(2, $products);
        $this->assertEquals('1', $products[0]->id());
        $this->assertEquals('Pasta', $products[0]->name());
        $this->assertEquals('2', $products[1]->id());
        $this->assertEquals('Papaya', $products[1]->name());
        $this->assertSame($products[0]::class, Product::class);
        $this->assertSame($products[1]::class, Product::class);
    }
}
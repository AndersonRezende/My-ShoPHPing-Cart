<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\UpdateProductInput;
use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class UpdateProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteUpdateProduct(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $createProductUseCase = new UpdateProductUseCase($productRepository);

        $product = $createProductUseCase->execute(new UpdateProductInput('1', 'Massa'));

        $this->assertNotNull($product->id());
        $this->assertEquals('Massa', $product->name());
    }
}
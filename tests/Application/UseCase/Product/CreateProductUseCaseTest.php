<?php declare(strict_types=1);

namespace Application\UseCase\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class CreateProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteCreateProduct(): void {
        $productRepository = new ProductRepositoryPdo($this->connection);
        $createProductUseCase = new CreateProductUseCase($productRepository);

        $product = $createProductUseCase->execute(new CreateProductInput('Pasta'));

        $this->assertNotNull($product->id());
        $this->assertEquals('Pasta', $product->name());
    }
}
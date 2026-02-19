<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\UpdateProductInput;
use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class UpdateProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteUpdateProduct(): void {
        $this->connection->exec("INSERT INTO categories (id, name) VALUES (1, 'Mercearia Seca (Grãos e Massas)')");
        $this->connection->exec("INSERT INTO categories (id, name) VALUES (2, 'Matinais e Sobremesas')");
        $this->connection->exec("INSERT INTO products (id, name, category_id) VALUES (1, 'Macarrão Penne', 2)");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $createProductUseCase = new UpdateProductUseCase($productRepository);

        $product = $createProductUseCase->execute(new UpdateProductInput('1', 'Macarrão Parafuso', '2'));

        $this->assertEquals('1', $product->id());
        $this->assertEquals('Macarrão Parafuso', $product->name());
        $this->assertEquals('2', $product->categoryId());
    }
}
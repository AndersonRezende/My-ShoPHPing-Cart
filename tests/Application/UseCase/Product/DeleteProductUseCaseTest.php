<?php declare(strict_types=1);

namespace Application\UseCase\Product;

use MyShoppingCart\Application\DTO\DeleteProductInput;
use MyShoppingCart\Application\UseCase\Product\DeleteProductUseCase;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

class DeleteProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteDeleteProduct(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $productRepository = new ProductRepositoryPdo($this->connection);
        $deleteProductUseCase = new DeleteProductUseCase($productRepository);
        $deleteProductUseCaseInput = new DeleteProductInput('1');

        $deleteProductUseCase->execute($deleteProductUseCaseInput);

        $stmt = $this->connection->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertFalse($row);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowExceptionWhenProductNotFound(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Product with ID 123 not found.');

        $pdoMock = $this->createMock(\PDO::class);
        $stmtMock = $this->createMock(\PDOStatement::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(false);

        $productRepository = new ProductRepositoryPdo($pdoMock);
        $deleteProductUseCase = new DeleteProductUseCase($productRepository);
        $deleteProductUseCaseInput = new DeleteProductInput('123');

        $deleteProductUseCase->execute($deleteProductUseCaseInput);
    }
}
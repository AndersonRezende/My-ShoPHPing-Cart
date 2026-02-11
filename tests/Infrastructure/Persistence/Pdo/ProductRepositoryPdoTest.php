<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Domain\Entity\Product;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use RuntimeException;

class ProductRepositoryPdoTest extends DatabaseTestCase {

    public function testSearchProductByTermWhenThereAreNoItems(): void {
        $repository = new ProductRepositoryPdo($this->connection);
        $results = $repository->search('non-existing-term');

        $this->assertCount(0, $results);
    }
    
    public function testSearchProductByTermWhenThereAreItemsWithSameTerm(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (2, 'Papaya')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (3, 'Egg')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (4, 'Eggplant')");

        $repository = new ProductRepositoryPdo($this->connection);
        $results = $repository->search('pa');

        $this->assertCount(2, $results);
        $this->assertEquals('1', $results[0]->id());
        $this->assertEquals('Pasta', $results[0]->name());
        $this->assertEquals('2', $results[1]->id());
        $this->assertEquals('Papaya', $results[1]->name());
        $this->assertSame($results[0]::class, Product::class);
    }

    public function testGetByIdWhenProductDoesNotExist(): void {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Product with ID 999 not found.');

        $repository = new ProductRepositoryPdo($this->connection);
        $repository->getById('999');
    }

    public function testGetByIdWhenProductExists(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES ('1', 'Pasta')");

        $repository = new ProductRepositoryPdo($this->connection);
        $product = $repository->getById('1');

        $this->assertEquals('1', $product->id());
        $this->assertEquals('Pasta', $product->name());
        $this->assertSame($product::class, Product::class);
    }

    public function testSaveNewProduct(): void {
        $repository = new ProductRepositoryPdo($this->connection);
        $product = new Product('1', 'Pasta');
        $result = $repository->save($product);

        $stmt = $this->connection->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($result, $product);
        $this->assertEquals('1', $row['id']);
        $this->assertEquals('Pasta', $row['name']);
    }

    public function testShouldThrowRuntimeExceptionWhenAnErrorOccursDuringSave(): void {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to save product.');

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($pdoStatementMock);
        $repository = new ProductRepositoryPdo($pdoMock);
        $product = new Product('1', 'Pasta');

        $repository->save($product);
    }

    public function testUpdateExistingProduct(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES ('1', 'Pasta')");
        $repository = new ProductRepositoryPdo($this->connection);
        $updatedProduct = new Product('1', 'Spaghetti');
        $result = $repository->save($updatedProduct);

        $stmt = $this->connection->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($result, $updatedProduct);
        $this->assertEquals('1', $row['id']);
        $this->assertEquals('Spaghetti', $row['name']);
    }

    public function testExecuteFindAllShouldReturnAllProducts(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (2, 'Papaya')");
        $this->connection->exec("INSERT INTO products (id, name) VALUES (3, 'Egg')");

        $repository = new ProductRepositoryPdo($this->connection);
        $results = $repository->findAll();

        $this->assertCount(3, $results);
        $this->assertEquals('1', $results[0]->id());
        $this->assertEquals('Pasta', $results[0]->name());
        $this->assertEquals('2', $results[1]->id());
        $this->assertEquals('Papaya', $results[1]->name());
        $this->assertEquals('3', $results[2]->id());
        $this->assertEquals('Egg', $results[2]->name());
        $this->assertSame($results[0]::class, Product::class);
    }

    public function testExecuteDeleteByIdShouldDeleteProduct(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES ('1', 'Pasta')");
        $repository = new ProductRepositoryPdo($this->connection);

        $repository->deleteById('1');

        $stmt = $this->connection->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertFalse($row);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteDeleteByIdWhenProductDoesNotExist(): void {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Product with ID 1234 not found.');

        $pdoMock = $this->createMock(\PDO::class);
        $stmtMock = $this->createMock(\PDOStatement::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(false);
        $repository = new ProductRepositoryPdo($pdoMock);

        $repository->deleteById('1234');
    }
}
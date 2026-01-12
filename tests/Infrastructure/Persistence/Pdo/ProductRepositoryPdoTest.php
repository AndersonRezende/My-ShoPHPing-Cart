<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductRepositoryPdoTest extends TestCase {

    public function testSearchProductByTermWhenThereAreNoItems(): void {
        $pdo = SqliteTestHelper::createConnection();
        
        $repository = new ProductRepositoryPdo($pdo);
        $results = $repository->search('non-existing-term');

        $this->assertCount(0, $results);
    }
    
    public function testSearchProductByTermWhenThereAreItemsWithSameTerm(): void {
        $pdo = SqliteTestHelper::createConnection();
        $pdo->exec("INSERT INTO products (id, name) VALUES (1, 'Pasta')");
        $pdo->exec("INSERT INTO products (id, name) VALUES (2, 'Papaya')");
        $pdo->exec("INSERT INTO products (id, name) VALUES (3, 'Egg')");
        $pdo->exec("INSERT INTO products (id, name) VALUES (4, 'Eggplant')");

        $repository = new ProductRepositoryPdo($pdo);
        $results = $repository->search('pa');

        $this->assertCount(2, $results);
        $this->assertEquals('1', $results[0]->id());
        $this->assertEquals('Pasta', $results[0]->name());
        $this->assertEquals('2', $results[1]->id());
        $this->assertEquals('Papaya', $results[1]->name());
        $this->assertSame($results[0]::class, Product::class);
    }

    public function testGetByIdWhenProductDoesNotExist(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Product with ID 999 not found.');

        $pdo = SqliteTestHelper::createConnection();

        $repository = new ProductRepositoryPdo($pdo);
        $repository->getById('999');
    }

    public function testGetByIdWhenProductExists(): void {
        $pdo = SqliteTestHelper::createConnection();
        $pdo->exec("INSERT INTO products (id, name) VALUES ('1', 'Pasta')");

        $repository = new ProductRepositoryPdo($pdo);
        $product = $repository->getById('1');

        $this->assertEquals('1', $product->id());
        $this->assertEquals('Pasta', $product->name());
        $this->assertSame($product::class, Product::class);
    }

    public function testSaveNewProduct(): void {
        $pdo = SqliteTestHelper::createConnection();
        $repository = new ProductRepositoryPdo($pdo);
        $product = new Product('1', 'Pasta');
        $result = $repository->save($product);

        $stmt = $pdo->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertTrue($result);
        $this->assertEquals('1', $row['id']);
        $this->assertEquals('Pasta', $row['name']);
    }

    public function testUpdateExistingProduct(): void {
        $pdo = SqliteTestHelper::createConnection();
        $pdo->exec("INSERT INTO products (id, name) VALUES ('1', 'Pasta')");
        $repository = new ProductRepositoryPdo($pdo);
        $updatedProduct = new Product('1', 'Spaghetti');
        $result = $repository->save($updatedProduct);

        $stmt = $pdo->query("SELECT * FROM products WHERE id = '1'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertTrue($result);
        $this->assertEquals('1', $row['id']);
        $this->assertEquals('Spaghetti', $row['name']);
    }
}
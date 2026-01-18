<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class CartRepositoryPdoTest extends DatabaseTestCase {
    
    public function testShouldNotReturnAnyCartItemWhenThereAreNoItems(): void {
        $repository = new CartRepositoryPdo($this->connection);
        $cart = $repository->findById('1');

        $this->assertNull($cart);
    }

    public function testShouldCreateNewCart(): void {
        $repository = new CartRepositoryPdo($this->connection);
        $cart = new CartBuilder()
            ->withId('1')
            ->withStatus(CartStatus::OPENED)
            ->build();
        $repository->save($cart);

        $stmt = $this->connection->query('SELECT COUNT(*) as item_count FROM cart_items WHERE cart_id = 1');
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(0, (int)$result['item_count']);
    }

    public function testShouldSaveCartWithItems(): void {
        $this->connection->exec("INSERT INTO products (id, name, category_id) VALUES (1, 'Product 1', null)");
        $repository = new CartRepositoryPdo($this->connection);
        $cart = new CartBuilder()
            ->withId('1')
            ->withStatus(CartStatus::OPENED)
            ->build();
        $product = new Product('1', $expectedProductName = 'Product 1');
        $unitPrice = new Money(500);
        $cartItem = new CartItem(null, $product, 2, $unitPrice);
        $cart->addItem($cartItem);
        $repository->save($cart);

        $stmt = $this->connection->query('SELECT ci.*, p.name as product_name FROM cart_items as ci JOIN products as p ON ci.product_id = p.id WHERE cart_id = 1');
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertCount(1, $items);
        $this->assertEquals('1', $items[0]['cart_id']);
        $this->assertEquals('1', $items[0]['product_id']);
        $this->assertEquals($expectedProductName, $items[0]['product_name']);
        $this->assertEquals(2, $items[0]['quantity']);
        $this->assertEquals(500, $items[0]['unit_price']);
    }

    public function testShouldReturnCartWithItemsWhenThereAreItems(): void {
        $this->connection->exec("INSERT INTO carts (id, status, created_at, updated_at) VALUES (1, 'opened', datetime('now'), datetime('now'))");
        $this->connection->exec("INSERT INTO products (id, name, category_id, created_at, updated_at) VALUES (1, 'Product 1', null, datetime('now'), datetime('now'))");
        $this->connection->exec("INSERT INTO cart_items (cart_id, product_id, quantity, unit_price) VALUES (1, 1, 2, 500)");
        
        $repository = new CartRepositoryPdo($this->connection);
        $cart = $repository->findById('1');

        $this->assertNotNull($cart);
        $this->assertEquals('1', $cart->id());
        $this->assertEquals(CartStatus::OPENED, $cart->status());
        $this->assertCount(1, $cart->items());
        $item = $cart->items()[0];
        $this->assertEquals('Product 1', $item->product()->name());
        $this->assertEquals(2, $item->quantity());
        $this->assertEquals(500, $item->unitPrice()->amount());
    }

    public function testErrorInTransactionShouldRollback(): void {
        $this->connection->commit();
        $this->connection->exec('PRAGMA foreign_keys = ON;');
        
        $repository = new CartRepositoryPdo($this->connection);
        $cart = new CartBuilder()
            ->withId('1')
            ->withStatus(CartStatus::OPENED)
            ->build();
        $product = new Product('9999', 'Product 1');
        $unitPrice = new Money(500);
        $cartItem = new CartItem(null, $product, 2, $unitPrice);
        $cart->addItem($cartItem);
        
        try {
            $repository->save($cart);
            $this->fail('Should thrown a PDOException for FK violation.');
        } catch (\PDOException $e) {
            $stmt = $this->connection->query('SELECT COUNT(*) FROM carts WHERE id = 1');
            $this->assertEquals(0, (int)$stmt->fetchColumn());
        }
    }
}
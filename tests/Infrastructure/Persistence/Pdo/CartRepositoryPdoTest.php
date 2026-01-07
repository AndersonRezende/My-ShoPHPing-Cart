<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Domain\Entity\Cart;
use PHPUnit\Framework\TestCase;

class CartRepositoryPdoTest extends TestCase {
    
    public function testShouldNotReturnAnyCartItemWhenThereAreNoItems(): void {
        $pdo = SqliteTestHelper::createConnection();
        
        $repository = new CartRepositoryPdo($pdo);
        $cart = $repository->get();

        $this->assertSame($cart::class, Cart::class);
        $this->assertCount(0, $cart->items());
    }

    public function testShouldReturnCartWithItemsWhenThereAreItems(): void {
        $pdo = SqliteTestHelper::createConnection();
        $pdo->exec("
            INSERT INTO cart_items (cart_id, product_id, description, quantity, unit_price)
            VALUES (1, 'p1', 'Product 1', 2, 500);
        ");
        $pdo->exec("
            INSERT INTO cart_items (cart_id, product_id, description, quantity, unit_price)
            VALUES (1, 'p2', 'Product 2', 1, 300);
        ");

        $repository = new CartRepositoryPdo($pdo);
        $cart = $repository->get();

        $this->assertSame($cart::class, Cart::class);
        $this->assertCount(2, $cart->items());
    }

    public function testShouldSaveCartWithItems(): void {
        $pdo = SqliteTestHelper::createConnection();
        $cart = new Cart();
        $repository = new CartRepositoryPdo($pdo);
        $repository->save($cart);

        $stmt = $pdo->query('SELECT COUNT(*) as item_count FROM cart_items WHERE cart_id = 1');
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(0, (int)$result['item_count']);
    }
}
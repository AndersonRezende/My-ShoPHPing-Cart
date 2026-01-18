<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;

use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Domain\Enum\CartStatus;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase {

    public function testCanAddItemsAndCalculateTotal(): void {
        $cart = new Cart();

        $product1 = new Product('prod-001', 'Product 1');
        $unitPrice1 = new Money(1500);
        $cartItem1 = new CartItem(null, $product1, 2, $unitPrice1);
        $cart->addItem($cartItem1);

        $product2 = new Product('prod-002', 'Product 2');
        $unitPrice2 = new Money(3000);
        $cartItem2 = new CartItem(null, $product2, 1, $unitPrice2);
        $cart->addItem($cartItem2);

        $total = $cart->total();

        $this->assertEquals(6000, $total->amount());
        $this->assertCount(2, $cart->items());
    }

    public function testTotalIsZeroWhenCartIsEmpty(): void {
        $cart = new Cart();

        $total = $cart->total();

        $this->assertEquals(0, $total->amount());
        $this->assertCount(0, $cart->items());
    }

    public function testCannotModifyClosedCart(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot modify a cart that is not opened');

        $cart = new Cart(status: CartStatus::COMPLETED);

        $product = new Product('prod-003', 'Product 3');
        $unitPrice = new Money(2000);
        $cartItem = new CartItem(null, $product, 1, $unitPrice);
        $cart->addItem($cartItem);
    }
}
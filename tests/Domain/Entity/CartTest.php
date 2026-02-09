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

    public function testCannotUpdateItemQuantityInClosedCart(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot modify a cart that is not opened');

        $cart = new Cart(status: CartStatus::COMPLETED);

        $cart->updateItemQuantity('prod-001', 5);
    }

    public function testShouldRemoveItemWhenQuantityIsZero(): void {
        $cart = new Cart();
        $product = new Product('prod-004', 'Product 4');
        $unitPrice = new Money(2500);
        $cartItem = new CartItem(null, $product, 2, $unitPrice);
        $cart->addItem($cartItem);

        $cart->updateItemQuantity('prod-004', 0);

        $this->assertCount(0, $cart->items());
    }
    
    public function testShouldThrowExceptionWhenItemNotInCart(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cart item not exists in the cart');

        $cart = new Cart();
        $product = new Product('prod-005', 'Product 5');
        $unitPrice = new Money(3000);
        $cartItem = new CartItem(null, $product, 1, $unitPrice);
        $cart->addItem($cartItem);
        $cart->updateItemQuantity('non-existent-product-id', 2);
    }

    public function testCannotRemoveItemFromNotOpenedCart(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot modify a cart that is not opened');

        $cart = new Cart(null, CartStatus::COMPLETED);

        $cart->removeItem('prod-004', 0);
    }

    public function testCannotFinalizeNotOpenedCart(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Only opened carts can be completed');

        $cart = new Cart(null, CartStatus::COMPLETED);

        $cart->finalize();
    }

    public function testCanFinalizeOpenedCart(): void {
        $cart = new Cart();

        $cart->finalize();

        $this->assertEquals(CartStatus::COMPLETED, $cart->status());
    }

    public function testCanRemoveUserFromCart(): void {
        $cart = new Cart();
        $cart->addUser('1');
        $cart->addUser('2');

        $cart->removeUser('1');

        $this->assertCount(1, $cart->userIds());
    }
}
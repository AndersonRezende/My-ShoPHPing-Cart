<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase {

    public function testCannotCreateCartItemWithZeroQuantity(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $product = new Product('prod-001', 'Sample Product');
        new CartItem('item-1', $product, 0, new Money(1000));
    }

    public function testCanCreateCartItemWithValidQuantity(): void {
        $product = new Product('prod-002', 'Another Product');
        $cartItem = new CartItem('item-1', $product, 2, new Money(1500));

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals('item-1', $cartItem->id());
        $this->assertEquals(2, $cartItem->quantity());
        $this->assertEquals($product, $cartItem->product());
    }

    public function testCalculateSubTotalReturnsCorrectAmount(): void {
        $product = new Product('prod-003', 'Third Product');
        $unitPrice = new Money(2000);
        $cartItem = new CartItem('item-1', $product, 3, $unitPrice);

        $subTotal = $cartItem->subTotal();

        $this->assertEquals(6000, $subTotal->amount());
    }

    public function testCanUpdateQuantity(): void {
        $product = new Product('prod-004', 'Fourth Product');
        $unitPrice = new Money(2500);
        $cartItem = new CartItem('item-1', $product, 2, $unitPrice);

        $cartItem->setQuantity(3);

        $this->assertEquals(3, $cartItem->quantity());
    }

    public function testCannotSetQuantityToZero(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $product = new Product('prod-004', 'Fourth Product');
        $unitPrice = new Money(2500);
        $cartItem = new CartItem('item-1', $product, 2, $unitPrice);

        $cartItem->setQuantity(0);
    }
}
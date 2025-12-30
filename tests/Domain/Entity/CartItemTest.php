<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase {

    public function testCannotCreateCartItemWithZeroQuantity(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $product = new Product('prod-001', 'Sample Product');
        new \MyShoppingCart\Domain\Entity\CartItem($product, 0, new \MyShoppingCart\Domain\ValueObject\Money(1000));
    }

    public function testCanCreateCartItemWithValidQuantity(): void {
        $product = new Product('prod-002', 'Another Product');
        $cartItem = new \MyShoppingCart\Domain\Entity\CartItem($product, 2, new \MyShoppingCart\Domain\ValueObject\Money(1500));

        $this->assertInstanceOf(\MyShoppingCart\Domain\Entity\CartItem::class, $cartItem);
    }

    public function testCalculateSubTotalReturnsCorrectAmount(): void {
        $product = new Product('prod-003', 'Third Product');
        $unitPrice = new \MyShoppingCart\Domain\ValueObject\Money(2000);
        $cartItem = new \MyShoppingCart\Domain\Entity\CartItem($product, 3, $unitPrice);

        $subTotal = $cartItem->subTotal();

        $this->assertEquals(6000, $subTotal->amount());
    }
}
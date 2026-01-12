<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity\Cart;

use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

class CartBuilderTest extends TestCase {

    public static function dataProviderBuildCartWithExpectedId(): iterable {
        yield 'Null ID' => [null];
        yield 'Valid ID' => ['cart-123'];
    }

    #[DataProvider('dataProviderBuildCartWithExpectedId')]
    public function testCanBuildCartWithExpectedId(?string $id): void {
        $cart = new CartBuilder()
		->withId($id)
		->build();

        $this->assertEquals($id, $cart->id());
        $this->assertInstanceOf(Cart::class, $cart);
    }
    
    public static function dataProviderBuildCartWithExpectedStatus(): iterable {
        yield [CartStatus::OPENED];
        yield [CartStatus::CANCELLED];
        yield [CartStatus::COMPLETED];
    }

    #[DataProvider('dataProviderBuildCartWithExpectedStatus')]
    public function testCanBuildCartWithExpectedStatus(CartStatus $status): void {
        $cart = new CartBuilder()
            ->withStatus($status)
            ->build();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($status, $cart->status());
    }

    public function testBuildCartWithOutStatusShouldCreateWithOpenedStatus(): void {
        $cart = new CartBuilder()->build();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(CartStatus::OPENED, $cart->status());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testCanBuildCartWithExpectedCartItems(): void {
        $cartItem1 = $this->createMock(CartItem::class);
        $cartItem2 = $this->createMock(CartItem::class);
        $cartItems = [$cartItem1, $cartItem2];

        $cart = new CartBuilder()
            ->withCartItems($cartItems)
            ->build();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(null, $cart->id());
        $this->assertCount(2, $cart->items());
    }
}

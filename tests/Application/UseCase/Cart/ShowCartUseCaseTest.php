<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\ShowCartInput;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class ShowCartUseCaseTest extends TestCase {

    public function testExecuteWhenCartDoesNotExist(): void {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("Cart not found");

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('nonexistent-cart-id')
            ->willThrowException(new ResourceNotFoundException('Cart not found'));
        $showCartUseCase = new ShowCartUseCase($cartRepository);

        $showCartUseCase->execute(new ShowCartInput('nonexistent-cart-id'));
    }

    public function testExecuteShouldThrowDomainExceptionWhenUserIsNotAllowedToAccess(): void {
        $this->expectExceptionMessage(DomainException::class);
        $this->expectExceptionMessage('User is not allowed to access this cart');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn(new CartBuilder()->withId('existing-cart-id')->withUserIds(['user-1'])->build());
        $showCartUseCase = new ShowCartUseCase($cartRepository);

        $showCartUseCase->execute(new ShowCartInput('cart-id', 'user-2'));
    }

    public function testExecuteWhenCartExists(): void {
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('existing-cart-id')
            ->willReturn(new CartBuilder()->withId('existing-cart-id')->withUserIds(['user-id'])->build());
        $showCartUseCase = new ShowCartUseCase($cartRepository);

        $cart = $showCartUseCase->execute(new ShowCartInput('existing-cart-id', 'user-id'));

        $this->assertEquals('existing-cart-id', $cart->id());
        $this->assertEquals(CartStatus::OPENED, $cart->status());
        $this->assertIsArray($cart->items());
    }

    public function testExecuteWhenCartExistsWithItems(): void {
        $cartItem = new CartItem(
            '1',
            new Product('prod-1', 'Product 1'),
            2,
            new Money(1500)
        );
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('existing-cart-id')
            ->willReturn(new CartBuilder()
                ->withId('existing-cart-id')
                ->withCartItems([$cartItem])
                ->withUserIds(['u-2'])
                ->build());
        $showCartUseCase = new ShowCartUseCase($cartRepository);

        $cart = $showCartUseCase->execute(new ShowCartInput('existing-cart-id', 'u-2'));

        $this->assertEquals('existing-cart-id', $cart->id());
        $this->assertEquals(CartStatus::OPENED, $cart->status());
        $this->assertCount(1, $cart->items());
        $this->assertEquals('prod-1', $cart->items()[0]->product()->id());
        $this->assertEquals('Product 1', $cart->items()[0]->product()->name());
        $this->assertEquals(2, $cart->items()[0]->quantity());
        $this->assertEquals(1500, $cart->items()[0]->unitPrice()->amount());
        $this->assertSame($cart->items()[0]->product()::class, Product::class);
    }
}

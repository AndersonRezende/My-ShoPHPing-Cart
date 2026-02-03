<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use LogicException;
use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use PHPUnit\Framework\TestCase;

class CheckoutCartUseCaseTest extends TestCase {

    public function testExecuteShouldThrownLogicExceptionWhenCartIsNotOpen(): void {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only opened carts can be completed');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('nonexistent-cart-id')
            ->willReturn(new CartBuilder()
                ->withId('nonexistent-cart-id')
                ->withStatus(CartStatus::COMPLETED)
                ->build());
        $checkoutCartUseCase = new CheckoutCartUseCase($cartRepository);

        $checkoutCartUseCase->execute(new CheckoutInput('nonexistent-cart-id'));
    }

    public function testExecuteShouldCheckoutCartSuccessfully(): void {
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('existing-cart-id')
            ->willReturn(
                new CartBuilder()
                ->withId('existing-cart-id')
                ->withStatus(CartStatus::OPENED)
                ->build()
            );
        $cartRepository->expects($this->once())
            ->method('save');
            
        $checkoutCartUseCase = new CheckoutCartUseCase($cartRepository);

        $cart = $checkoutCartUseCase->execute(new CheckoutInput('existing-cart-id'));
        

        $this->assertEquals(CartStatus::COMPLETED, $cart->status());
    }
}

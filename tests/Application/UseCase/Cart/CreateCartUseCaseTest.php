<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Application\UseCase\Cart\CreateCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use PHPUnit\Framework\TestCase;

class CreateCartUseCaseTest extends TestCase {

    public function testExecuteShouldCreateNewCart(): void {
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('save');
        $checkoutCartUseCase = new CreateCartUseCase($cartRepository);

        $outputCart = $checkoutCartUseCase->execute(new CreateCartInput());

        $this->assertInstanceOf(Cart::class, $outputCart);
        $this->assertEquals(CartStatus::OPENED, $outputCart->status());
    }
}

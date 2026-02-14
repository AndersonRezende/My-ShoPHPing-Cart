<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Application\UseCase\Cart\CreateCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use PHPUnit\Framework\TestCase;

class CreateCartUseCaseTest extends TestCase {

    public function testExecuteShouldCreateNewCart(): void {
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('save');
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn('uuid-1234');

        $createCartUseCase = new CreateCartUseCase($cartRepository, $idGenerator);
        $outputCart = $createCartUseCase->execute(new CreateCartInput());

        $this->assertInstanceOf(Cart::class, $outputCart);
        $this->assertEquals('uuid-1234', $outputCart->id());
        $this->assertEquals(CartStatus::OPENED, $outputCart->status());
    }
}

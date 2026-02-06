<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\UserRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class AssociateUserToCartUseCaseTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testAssociateUserToCart(): void {
        $cart = new Cart('cart-123');
        $user = $this->createMock(User::class);

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('cart-123')
            ->willReturn($cart);
        $cartRepository->expects($this->once())
            ->method('save')
            ->with($cart);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with('user-123')
            ->willReturn($user);

        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute('cart-123', 'user-123');

        $this->assertContains('user-123', $cart->userIds());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowExceptionWhenCartNotFound(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cart not found');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn(null);
        $userRepository = $this->createMock(UserRepository::class);

        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute('cart-123', 'user-123');
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowExceptionWhenUserNotFound(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User not found');

        $cart = new Cart('cart-123');
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('cart-123')
            ->willReturn($cart);
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with('user-123')
            ->willReturn(null);

        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute('cart-123', 'user-123');
    }
}

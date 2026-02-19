<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\AssociateUserToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\UserRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class AssociateUserToCartUseCaseTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testAssociateUserToCart(): void {
        $cart = new CartBuilder()
            ->withId('cart-123')
            ->withUserIds(['u-1'])
            ->build();
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('id')->willReturn('u-2');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
        $cartRepository->expects($this->once())
            ->method('save')
            ->with($cart);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with('u-2')
            ->willReturn($user);

        $input = new AssociateUserToCartInput('cart-123', 'u-1', 'u-2');
        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute($input);

        $this->assertContains('u-1', $cart->userIds());
        $this->assertContains('u-2', $cart->userIds());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowExceptionWhenCartNotFound(): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cart not found');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willThrowException(new ResourceNotFoundException('Cart not found'));
        $userRepository = $this->createMock(UserRepository::class);

        $input = new AssociateUserToCartInput('cart-123', 'u-1', 'u-2');
        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute($input);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowDomainExceptionWhenUserIsNotAuthorized(): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User is not allowed to access this cart');

        $user = $this->createMock(User::class);
        $cart = new CartBuilder()
            ->withId('cart-123')
            ->withUserIds(['u-1'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('cart-123')
            ->willReturn($cart);
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with('u-3')
            ->willReturn($user);

        $input = new AssociateUserToCartInput('cart-123', 'u-2', 'u-3');
        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute($input);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldThrowExceptionWhenUserNotFound(): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User not found');

        $cart = new CartBuilder()
            ->withId('cart-123')
            ->withUserIds(['u-1'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->with('cart-123')
            ->willReturn($cart);
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findById')
            ->with('u-2')
            ->willThrowException(new ResourceNotFoundException('User not found'));

        $input = new AssociateUserToCartInput('cart-123', 'u-1', 'u-2');
        $useCase = new AssociateUserToCartUseCase($cartRepository, $userRepository);
        $useCase->execute($input);
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\UserRepository;

readonly class AssociateUserToCartUseCase {
    public function __construct(
        private CartRepository $cartRepository,
        private UserRepository $userRepository
    ) {}

    public function execute(string $cartId, string $userId): void {
        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new DomainException('User not found');
        }

        $cart->addUser($userId);
        $this->cartRepository->save($cart);
    }
}

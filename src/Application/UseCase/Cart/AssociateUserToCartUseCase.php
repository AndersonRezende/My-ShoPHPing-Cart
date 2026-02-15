<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\AssociateUserToCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\UserRepository;

readonly class AssociateUserToCartUseCase {
    public function __construct(
        private CartRepository $cartRepository,
        private UserRepository $userRepository
    ) {}

    public function execute(AssociateUserToCartInput $input): void {
        $cart = $this->cartRepository->findById($input->cartId);
        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        if (!$cart->isUserAllowedToAccess($input->ownerUserId)) {
            throw new DomainException('User is not allowed to access this cart');
        }

        $user = $this->userRepository->findById($input->userId);
        if (!$user) {
            throw new DomainException('User not found');
        }

        $cart->addUser($input->userId);
        $this->cartRepository->save($cart);
    }
}

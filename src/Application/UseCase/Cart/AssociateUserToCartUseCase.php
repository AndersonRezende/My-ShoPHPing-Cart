<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\AssociateUserToCartInput;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\UserRepository;

readonly class AssociateUserToCartUseCase {
    public function __construct(
        private CartRepository $cartRepository,
        private UserRepository $userRepository
    ) {}

    /** @throws ResourceNotFoundException */
    public function execute(AssociateUserToCartInput $input): void {
        $cart = $this->cartRepository->findById($input->cartId);
        $user = $this->userRepository->findById($input->userId);

        if (!$cart->isUserAllowedToAccess($input->ownerUserId)) {
            throw new DomainException('User is not allowed to access this cart');
        }

        $cart->addUser($user->id());
        $this->cartRepository->save($cart);
    }
}

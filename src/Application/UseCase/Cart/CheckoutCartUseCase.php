<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use LogicException;
use MyShoppingCart\Application\DTO\CheckoutCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

readonly class CheckoutCartUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    /** @throws LogicException|DomainException */
    public function execute(CheckoutCartInput $input): Cart {
        $cart = $this->cartRepository->findById($input->cartId);
        if (!$cart->isUserAllowedToAccess($input->userId)) {
            throw new DomainException('User is not allowed to access this cart');
        }
        $cart->finalize();
        $this->cartRepository->save($cart);
        return $cart;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use LogicException;
use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

readonly class CheckoutCartUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    /** @throws LogicException */
    public function execute(CheckoutInput $input): Cart {
        $cart = $this->cartRepository->findById($input->cartId);
        $cart->finalize();
        $this->cartRepository->save($cart);
        return $cart;
    }
}

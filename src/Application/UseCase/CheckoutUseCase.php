<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

class CheckoutUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    public function execute(CheckoutInput $input): Cart {
        $cart = $this->cartRepository->getById($input->cartId);
        $cart->finalize();
        return $this->cartRepository->save($cart);
    }
}

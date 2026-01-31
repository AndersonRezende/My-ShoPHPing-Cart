<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

readonly class CheckoutUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    public function execute(CheckoutInput $input): Cart {
        $cart = $this->cartRepository->findById($input->cartId);
        $cart->finalize();
        return $this->cartRepository->save($cart);
    }
}

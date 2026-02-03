<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

readonly class CreateCartUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    public function execute(CreateCartInput $input): Cart {
        $cart = new Cart(); 
        $this->cartRepository->save($cart);
        return $cart;
    }
}

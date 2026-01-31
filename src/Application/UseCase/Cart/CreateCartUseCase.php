<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

class CreateCartUseCase {
    public function __construct(private CartRepository $cartRepository) {}

    public function execute(CreateCartInput $input): Cart {
        $cart = new Cart(); 
        return $this->cartRepository->save($cart);
    }
}

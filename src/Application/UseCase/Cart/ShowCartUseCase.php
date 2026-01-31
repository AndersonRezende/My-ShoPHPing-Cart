<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use InvalidArgumentException;
use MyShoppingCart\Application\DTO\ShowCartInput;
use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

class ShowCartUseCase {

    public function __construct(private CartRepository $repository) {}

    /** @throws InvalidArgumentException */
    public function execute(ShowCartInput $showCartInput): Cart {
        $cart = $this->repository->findById($showCartInput->cartId);
        if ($cart === null) {
            throw new InvalidArgumentException("Cart not found");
        }

       return $cart;
    }
}
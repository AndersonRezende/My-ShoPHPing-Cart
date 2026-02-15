<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use InvalidArgumentException;
use MyShoppingCart\Application\DTO\ShowCartInput;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

readonly class ShowCartUseCase {

    public function __construct(private CartRepository $repository) {}

    /** @throws InvalidArgumentException */
    public function execute(ShowCartInput $showCartInput): Cart {
        $cart = $this->repository->findById($showCartInput->cartId);
        if ($cart === null) {
            throw new InvalidArgumentException("Cart not found");
        }

        if (!empty($cart->userIds()) && $showCartInput->userId !== null) {
            if (!in_array($showCartInput->userId, $cart->userIds())) {
                throw new \DomainException("Access denied: You are not the owner of this cart.");
            }
        }

        if (!empty($cart->userIds()) && $showCartInput->userId === null) {
            throw new \DomainException("Access denied: This cart belongs to a registered user.");
        }

       return $cart;
    }
}

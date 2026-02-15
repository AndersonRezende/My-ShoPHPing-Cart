<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
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
            throw new InvalidArgumentException('Cart not found');
        }

        if (!$cart->isUserAllowedToAccess($showCartInput->userId)) {
            throw new DomainException('User is not allowed to access this cart');
        }
       return $cart;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\RemoveItemFromCartInput;
use MyShoppingCart\Application\Repository\CartRepository;

class RemoveItemFromCart {
    
    public function __construct(private CartRepository $repository) {}

    public function execute(RemoveItemFromCartInput $input): void {
        $cart = $this->repository->findById($input->cartId);

        if (!$cart) {
            throw new \InvalidArgumentException('Cart not found');
        }

        $cart->removeItem($input->productId);
        
        $this->repository->save($cart);
    }
}
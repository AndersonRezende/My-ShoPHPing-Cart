<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\UpdateItemQuantityInput;
use MyShoppingCart\Application\Repository\CartRepository;

readonly class UpdateItemQuantity {
    
    public function __construct(private CartRepository $repository) {}

    public function execute(UpdateItemQuantityInput $input): void {
        $cart = $this->repository->findById($input->cartId);

        if (!$cart) {
            throw new \InvalidArgumentException('Cart not found');
        }

        $cart->updateItemQuantity($input->productId, $input->newQuantity);
        
        $this->repository->save($cart);
    }
}
<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\UpdateItemQuantityInput;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;

readonly class UpdateItemQuantityInCartUseCase {
    
    public function __construct(private CartRepository $repository) {}

    /** @throws ResourceNotFoundException|DomainException */
    public function execute(UpdateItemQuantityInput $input): void {
        $cart = $this->repository->findById($input->cartId);

        $cart->updateItemQuantity($input->productId, $input->newQuantity);
        $this->repository->save($cart);
    }
}

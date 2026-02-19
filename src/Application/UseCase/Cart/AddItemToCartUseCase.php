<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\AddItemToCartInput;
use MyShoppingCart\Application\DTO\CartOutput;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use MyShoppingCart\Domain\ValueObject\Money;

readonly class AddItemToCartUseCase {
    
    public function __construct(
            private CartRepository $cartRepository,
            private ProductRepository $productRepository,
            private IdGeneratorInterface $idGenerator
    ) {}

    /** @throws ResourceNotFoundException|DomainException */
    public function execute(AddItemToCartInput $input): CartOutput {
        $cart = $this->cartRepository->findById($input->cartId);

        if (!$cart->isUserAllowedToAccess($input->userId)) {
            throw new DomainException('Access denied: You can not modify this cart.');
        }

        $product = $this->productRepository->getById($input->productId);
        $unitPrice = new Money($input->unitPrice);
        
        $id = $this->idGenerator->generate();
        $cartItem = new CartItem($id, $product, $input->quantity, $unitPrice);
        
        $cart->addItem($cartItem);
        $this->cartRepository->save($cart);
        
        return new CartOutput($cart->total()->amount(), $cart->items());
    }
}

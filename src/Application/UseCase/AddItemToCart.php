<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\DTO\AddItemInput;
use MyShoppingCart\Application\DTO\CartOutput;
use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\ValueObject\Money;

class AddItemToCart {
    
    public function __construct(
            private CartRepository $cartRepository,
            private ProductRepository $productRepository,
    ) {}
    
    public function execute(AddItemInput $input): CartOutput {
        $cart = $this->cartRepository->get();
        
        $product = $this->productRepository->getById($input->productId);
        $unitPrice = new Money($input->unitPrice);
        $cartItem = new CartItem($product, $input->quantity, $unitPrice);
        
        $cart->addItem($cartItem);
        $this->cartRepository->save($cart);
        
        return new CartOutput($cart->total()->amount(), $cart->items());
    }
}

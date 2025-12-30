<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;

class CartItem {
    public function __construct(
        private Product $product, 
        private int $quantity,
        private Money $unitPrice
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }
    }

    public function subTotal(): Money {
        return $this->unitPrice->multiply($this->quantity);
    }
}
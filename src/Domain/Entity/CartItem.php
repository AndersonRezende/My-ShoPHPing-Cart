<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;

class CartItem {
    public function __construct(
        private string $id,
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

    public function id(): string {
        return $this->id;
    }

    public function product(): Product {
        return $this->product;
    }

    public function quantity(): int {
        return $this->quantity;
    }

    public function unitPrice(): Money {
        return $this->unitPrice;
    }

    public function setQuantity(int $quantity): void {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }
        $this->quantity = $quantity;
    }
}
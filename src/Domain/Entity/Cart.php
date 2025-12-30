<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;
use MyShoppingCart\Domain\ValueObject\Money;

class Cart {

    /** @var CartItem[] */
    private array $items = [];

    public function addItem(CartItem $item): void {
        $this->items[] = $item;
    }

    public function total(): Money {
        $total = new Money(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->subTotal());
        }
        return $total;
    }

    public function items(): array {
        return $this->items;
    }
}
<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Domain\Enum\CartStatus;

class Cart {

    /** @var CartItem[] */
    private array $items = [];

    public function __construct(
        private ?string $id = null,
        private CartStatus $status = CartStatus::OPENED
    ) {}

    public function addItem(CartItem $item): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new \LogicException('Cannot modify a cart that is not opened');
        }

        $this->items[] = $item;
    }

    public function finalize(): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new \LogicException('Only opened carts can be completed');
        }

        $this->status = CartStatus::COMPLETED;
    }

    public function total(): Money {
        $total = new Money(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->subTotal());
        }
        return $total;
    }

    public function id(): ?string {
        return $this->id;
    }

    public function status(): CartStatus {
        return $this->status;
    }

    /** @var CartItem[] */
    public function items(): array {
        return $this->items;
    }
}
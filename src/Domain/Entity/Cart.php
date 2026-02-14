<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use LogicException;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Domain\Enum\CartStatus;

class Cart {

    /** @var CartItem[] */
    private array $items = [];

    /** @var string[] */
    private array $userIds = [];

    public function __construct(
        private readonly string $id,
        private CartStatus $status = CartStatus::OPENED
    ) {}

    /** @throws LogicException */
    public function addItem(CartItem $item): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new LogicException('Cannot modify a cart that is not opened');
        }

        $this->items[] = $item;
    }

    /** @throws LogicException */
    public function updateItemQuantity(string $productId, int $quantity): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new LogicException('Cannot modify a cart that is not opened');
        }

        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        foreach ($this->items as $item) {
            if ($item->product()->id() === $productId) {
                $item->setQuantity($quantity);
                return;
            }
        }

        throw new \DomainException('Cart item not exists in the cart');
    }

    /** @throws LogicException */
    public function removeItem(string $productId): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new LogicException('Cannot modify a cart that is not opened');
        }

        foreach ($this->items as $index => $item) {
            if ($item->product()->id() === $productId) {
                unset($this->items[$index]);
                return;
            }
        }

        throw new \DomainException('Cart item not exists in the cart');
    }

    /** @throws LogicException */
    public function finalize(): void {
        if ($this->status !== CartStatus::OPENED) {
            throw new LogicException('Only opened carts can be completed');
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

    public function id(): string {
        return $this->id;
    }

    public function status(): CartStatus {
        return $this->status;
    }

    /** @return null|CartItem[] */
    public function items(): ?array {
        return $this->items;
    }

    public function addUser(string $userId): void {
        if (!in_array($userId, $this->userIds)) {
            $this->userIds[] = $userId;
        }
    }

    public function removeUser(string $userId): void {
        $key = array_search($userId, $this->userIds);
        if ($key !== false) {
            unset($this->userIds[$key]);
        }
    }

    /** @return string[] */
    public function userIds(): array {
        return $this->userIds;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity\Cart;

use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Enum\CartStatus;

class CartBuilder {

    private ?string $id = null;

    private CartStatus $status = CartStatus::OPENED;

    /** @var CartItem[] */
    private array $items = [];

    /** @var string[] */
    private array $userIds = [];

    public function withId(?string $id): self {
        $this->id = $id;
        return $this;
    }

    public function withStatus(CartStatus $status): self {
        $this->status = $status;
        return $this;
    }

    /** @param CartItem[] $items */
    public function withCartItems(array $items): self {
        $this->items = $items;
        return $this;
    }

    /** @param string[] $userIds */
    public function withUserIds(array $userIds): self {
        $this->userIds = $userIds;
        return $this;
    }

    public function build(): Cart {
        $cart = new Cart($this->id, $this->status);
        foreach ($this->items as $item) {
            $cart->addItem($item);
        }
        foreach ($this->userIds as $userId) {
            $cart->addUser($userId);
        }

        return $cart;
    }
}

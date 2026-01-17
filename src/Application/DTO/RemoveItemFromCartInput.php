<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class RemoveItemFromCartInput {
    public function __construct(
        public string $cartId,
        public string $productId
    ) {}
}
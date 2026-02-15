<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

final readonly class AddItemToCartInput {
    public function __construct(
            public string $cartId,
            public string $userId,
            public string $productId,
            public string $description,
            public int $quantity,
            public int $unitPrice
    ) {}
}
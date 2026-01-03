<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

final readonly class AddItemInput {
    public function __construct(
            public ?string $productId,
            public string $description,
            public int $quantity,
            public int $unitPrice
    ) {}
}
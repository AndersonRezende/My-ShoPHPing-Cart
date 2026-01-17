<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

final readonly class UpdateItemQuantityInput {
    public function __construct(
            public string $cartId,
            public string $productId,
            public int $newQuantity
    ) {}
}
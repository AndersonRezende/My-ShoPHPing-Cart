<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

final readonly class ShowCartInput {
    public function __construct(
            public string $cartId,
    ) {}
}
<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class CheckoutInput {
    public function __construct(
        public string $cartId
    ) {}
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class AssociateUserToCartInput {
    public function __construct(
        public string $cartId,
        public string $ownerUserId,
        public string $userId,
    ) {}
}

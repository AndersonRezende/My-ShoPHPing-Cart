<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class DeleteProductInput {
    public function __construct(
        public string $id
    ) {}
}

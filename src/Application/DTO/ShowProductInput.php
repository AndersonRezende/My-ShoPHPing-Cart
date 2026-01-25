<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class ShowProductInput {
    public function __construct(
        public string $id
    ) {}
}

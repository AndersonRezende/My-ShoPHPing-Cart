<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class UpdateProductInput {
    public function __construct(
        public string $id,
        public string $name,
        public ?string $categoryId = null,
    ) {}
}

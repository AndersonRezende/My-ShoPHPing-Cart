<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

final readonly class CreateProductInput {
    public function __construct(
        public string $name
    ) {}
}
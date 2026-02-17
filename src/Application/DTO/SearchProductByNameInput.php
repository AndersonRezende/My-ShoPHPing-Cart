<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

class SearchProductByNameInput {
    public function __construct(
        public string $name,
    ) {}
}
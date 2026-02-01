<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

use MyShoppingCart\Domain\Entity\Product;

final readonly class CartOutput {
    
    /**
     * @param int $total
     * @param Product[] $items
     */
    public function __construct(
            public int $total,
            public array $items
    ) {}
}

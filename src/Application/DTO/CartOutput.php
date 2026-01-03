<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

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

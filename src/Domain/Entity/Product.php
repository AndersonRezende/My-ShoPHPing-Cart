<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

final class Product {
    public function __construct(
        private readonly string $id, private readonly string $name
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('Product name cannot be empty');
        }
    }

    public function id(): string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }
}
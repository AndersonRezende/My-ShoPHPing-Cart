<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

class Product {
    public function __construct(
        private string $id,
        private string $name,
        private ?string $categoryId = null
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

    public function categoryId(): ?string {
        return $this->categoryId;
    }
}

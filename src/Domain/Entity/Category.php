<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

class Category {

    public function __construct(
        private ?string $id,
        private string $name
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('Category name cannot be empty');
        }
    }

    public function id(): ?string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }
}
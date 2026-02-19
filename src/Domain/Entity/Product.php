<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use InvalidArgumentException;

class Product {
    public function __construct(
        private string $id,
        private string $name,
        private ?string $categoryId = null
    ) {
        $this->throwExceptionIfInvalidName($name);
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

    public function rename(string $name): void {
        $this->throwExceptionIfInvalidName($name);
        $this->name = $name;
    }

    public function moveToCategory(?string $categoryId): void {
        $this->categoryId = $categoryId;
    }

    private function throwExceptionIfInvalidName(string $name): void {
        if (empty($name)) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }
    }
}

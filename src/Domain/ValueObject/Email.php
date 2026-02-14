<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\ValueObject;

use InvalidArgumentException;

readonly class Email {

    /** @throws InvalidArgumentException */
    public function __construct(private string $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }

    public function value(): string {
        return $this->value;
    }

    public function __toString(): string {
        return $this->value;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\ValueObject;

use InvalidArgumentException;

readonly class Password {
    public function __construct(private string $value) {
        if (strlen($value) < 6) {
            throw new InvalidArgumentException('Password must be at least 6 characters long');
        }
    }

    public function value(): string {
        return $this->value;
    }

    public function verify(string $plainPassword): bool {
        return password_verify($plainPassword, $this->value);
    }

    public static function hash(string $plainPassword): self {
        return new self(password_hash($plainPassword, PASSWORD_DEFAULT));
    }
}

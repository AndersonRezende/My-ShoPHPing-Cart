<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\ValueObject;

final class Money {
    private int $amount; // amount in cents
    
    public function __construct(int $amount) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
        $this->amount = $amount;
    }

    public function amount(): int {
        return $this->amount;
    }

    public function add(Money $other): Money {
        return new Money($this->amount + $other->amount());
    }

    public function multiply(int $factor): Money {
        if ($factor < 0) {
            throw new \InvalidArgumentException('Factor cannot be negative');
        }
        return new Money($this->amount * $factor);
    }
}
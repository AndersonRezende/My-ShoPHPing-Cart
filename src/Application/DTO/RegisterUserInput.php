<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class RegisterUserInput {
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class LoginUserInput {
    public function __construct(public string $email, public string $password) {}
}

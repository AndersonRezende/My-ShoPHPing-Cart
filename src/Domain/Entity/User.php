<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Entity;

use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;

class User {
    public function __construct(
        private string $id,
        private string $name,
        private Email $email,
        private Password $password
    ) {}

    public function id(): string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function email(): Email {
        return $this->email;
    }

    public function password(): Password {
        return $this->password;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Repository;

use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;

interface UserRepository {
    public function save(User $user): void;
    public function findById(string $id): ?User;
    public function findByEmail(Email $email): ?User;
}

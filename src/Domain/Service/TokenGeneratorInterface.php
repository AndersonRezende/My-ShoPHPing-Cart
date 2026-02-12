<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Service;

use MyShoppingCart\Domain\Entity\User;

interface TokenGeneratorInterface {
    public function generate(User $user): string;
}

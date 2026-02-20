<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Repository;

use MyShoppingCart\Domain\Entity\Cart;

interface CartRepository {
    public function save(Cart $cart): void;

    public function findById(string $id): Cart;
}

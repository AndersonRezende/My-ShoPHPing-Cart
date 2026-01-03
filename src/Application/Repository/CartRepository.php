<?php declare(strict_types=1);

namespace MyShoppingCart\Application\Repository;

use MyShoppingCart\Domain\Entity\Cart;

interface CartRepository {
    
    public function get(): Cart;
    
    public function save(Cart $cart): void;
}

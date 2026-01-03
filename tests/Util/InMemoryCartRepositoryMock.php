<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Util;

use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;

class InMemoryCartRepositoryMock implements CartRepository {

    private Cart $cart;

    public function __construct() {
        $this->cart = new Cart();
    }
    
    public function get(): Cart {
        return $this->cart;
    }
    
    public function save(Cart $cart): void {
        $this->cart = $cart;
    }
}
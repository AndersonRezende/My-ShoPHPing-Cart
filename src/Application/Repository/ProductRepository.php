<?php declare(strict_types=1);

namespace MyShoppingCart\Application\Repository;

use MyShoppingCart\Domain\Entity\Product;

interface ProductRepository {
 
    /** @return Product[] */
    public function search(string $term): array;
    
    public function getById(string $id): Product;
}
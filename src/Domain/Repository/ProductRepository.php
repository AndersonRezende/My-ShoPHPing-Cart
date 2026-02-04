<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Repository;

use MyShoppingCart\Domain\Entity\Product;

interface ProductRepository {
 
    /** @return Product[] */
    public function search(string $term): array;
    
    public function getById(string $id): Product;

    public function save(Product $product): Product;

    /** @return Product[] */
    public function findAll(): array;

    public function deleteById(string $id): void;
}
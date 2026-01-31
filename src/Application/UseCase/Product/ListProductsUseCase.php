<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Application\UseCase\Product;

class ListProductsUseCase {

    public function __construct(private ProductRepository $productRepository) {}

    /** @return Product[] */
    public function execute(): array {
        return $this->productRepository->findAll();
    }
}
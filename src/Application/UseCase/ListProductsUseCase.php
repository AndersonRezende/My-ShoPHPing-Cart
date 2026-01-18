<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\Repository\ProductRepository;

class ListProductsUseCase {

    public function __construct(private ProductRepository $productRepository) {}

    /** @return Product[] */
    public function execute(): array {
        return $this->productRepository->findAll();
    }
}
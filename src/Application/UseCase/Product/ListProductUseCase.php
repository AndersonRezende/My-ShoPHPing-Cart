<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class ListProductUseCase {

    public function __construct(private ProductRepository $productRepository) {}

    /** @return Product[] */
    public function execute(): array {
        return $this->productRepository->findAll();
    }
}
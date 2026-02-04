<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Domain\Entity\Product as ProductEntity;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class ListProductsUseCase {

    public function __construct(private ProductRepository $productRepository) {}

    /** @return ProductEntity[] */
    public function execute(): array {
        return $this->productRepository->findAll();
    }
}
<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\DTO\ShowProductInput;
use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Domain\Entity\Product;

class ShowProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}

    public function execute(ShowProductInput $input): Product {
        $product = $this->productRepository->getById($input->id);

        return $product;
    }
}

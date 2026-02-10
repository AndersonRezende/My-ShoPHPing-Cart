<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\ShowProductInput;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class ShowProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}

    public function execute(ShowProductInput $input): Product {
        return $this->productRepository->getById($input->id);
    }
}

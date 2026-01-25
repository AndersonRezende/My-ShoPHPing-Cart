<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\DTO\DeleteProductInput;
use MyShoppingCart\Application\Repository\ProductRepository;

class DeleteProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}

    public function execute(DeleteProductInput $input): void {
        $this->productRepository->deleteById($input->id);
    }
}

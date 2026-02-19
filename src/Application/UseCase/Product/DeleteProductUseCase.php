<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\DeleteProductInput;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class DeleteProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}

    /** @throws ResourceNotFoundException */
    public function execute(DeleteProductInput $input): void {
        $this->productRepository->deleteById($input->id);
    }
}

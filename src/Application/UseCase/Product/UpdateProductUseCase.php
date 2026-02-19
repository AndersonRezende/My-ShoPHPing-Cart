<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\UpdateProductInput;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class UpdateProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}

    public function execute(UpdateProductInput $input): Product {
        $product = $this->productRepository->getById($input->id);
        $product->rename($input->name);
        $product->moveToCategory($input->categoryId);
        return $this->productRepository->save($product);
    }
}

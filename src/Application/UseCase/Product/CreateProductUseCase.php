<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class CreateProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}
    
    public function execute(CreateProductInput $input): Product {
        $product = new Product(null, $input->name);
        return $this->productRepository->save($product);
    }
}
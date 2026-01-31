<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Domain\Entity\Product;

class CreateProductUseCase {
    public function __construct(private ProductRepository $productRepository) {}
    
    public function execute(CreateProductInput $input): Product {
        $product = new Product(null, $input->name);
        $product = $this->productRepository->save($product);
        return $product;
    }
}
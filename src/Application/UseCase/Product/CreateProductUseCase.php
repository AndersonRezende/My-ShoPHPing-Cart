<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;

readonly class CreateProductUseCase {
    public function __construct(
        private ProductRepository $productRepository,
        private IdGeneratorInterface $idGenerator
    ) {}
    
    public function execute(CreateProductInput $input): Product {
        $id = $this->idGenerator->generate();
        $product = new Product($id, $input->name);
        $this->productRepository->save($product);
        return $product;
    }
}

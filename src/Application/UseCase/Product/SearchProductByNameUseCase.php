<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Product;

use LogicException;
use MyShoppingCart\Application\DTO\SearchProductByNameInput;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;

readonly class SearchProductByNameUseCase {

    public function __construct(private ProductRepository $productRepository) {}

    /**
     * @return Product[]
     * @throws LogicException
     */
    public function execute(SearchProductByNameInput $input): array {
        $product = $this->productRepository->findByName($input->name);
        if (!$product) {
            throw new LogicException('Product not found');
        }
        return $product;
    }

}
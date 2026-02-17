<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class CreateProductUseCaseTest extends DatabaseTestCase {

    public function testExecuteCreateProduct(): void {
        $productRepository = new ProductRepositoryPdo($this->connection);
        $uuidGenerator = $this->createMock(IdGeneratorInterface::class);
        $uuidGenerator->expects($this->once())->method('generate')->willReturn('1');
        $createProductUseCase = new CreateProductUseCase($productRepository, $uuidGenerator);

        $product = $createProductUseCase->execute(new CreateProductInput('Pasta'));

        $this->assertNotNull($product->id());
        $this->assertEquals('1', $product->id());
        $this->assertEquals('Pasta', $product->name());
    }
}
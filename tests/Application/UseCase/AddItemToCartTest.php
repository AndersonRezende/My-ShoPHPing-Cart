<?php declare(strict_types=1);

namespace MyShoppingCart\Test\Application\UseCase;

use MyShoppingCart\Application\UseCase\AddItemToCart;
use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Application\DTO\AddItemInput;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Tests\Util\InMemoryCartRepositoryMock;
use PHPUnit\Framework\TestCase;

class AddItemToCartTest extends TestCase {

    public function testExecuteWithOneProduct(): void {
        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('getById')
            ->willReturn(new Product('prod-1', 'Product 1'));
        
        $input = new AddItemInput('prod-1', 'Product 1', 2, 1500);
        $addItemToCart = new AddItemToCart($cartRepository, $productRepository);
        $output = $addItemToCart->execute($input);

        $this->assertEquals(3000, $output->total);
        $this->assertCount(1, $output->items);
    }

    public function testExecuteWithMultipleProducts(): void {
        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('getById')
            ->willReturnCallback(function ($id) {
                return new Product($id, 'Product ' . substr($id, -1));
            });
        
        $addItemToCart = new AddItemToCart($cartRepository, $productRepository);
        
        $input1 = new AddItemInput('prod-1', 'Product 1', 1, 1000);
        $addItemToCart->execute($input1);
        
        $input2 = new AddItemInput('prod-2', 'Product 2', 3, 2000);
        $output = $addItemToCart->execute($input2);

        $this->assertEquals(7000, $output->total);
        $this->assertCount(2, $output->items);
    }

    public function testExecutingWithZeroQuantityMustThrowInvalidArgumentException(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('getById')
            ->willReturn(new Product('prod-1', 'Product 1'));
        
        $input = new AddItemInput('prod-1', 'Product 1', 0, 1500);
        $addItemToCart = new AddItemToCart($cartRepository, $productRepository);
        $output = $addItemToCart->execute($input);
    }   
}
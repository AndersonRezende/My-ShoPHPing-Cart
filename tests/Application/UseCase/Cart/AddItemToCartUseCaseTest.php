<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use InvalidArgumentException;
use LogicException;
use MyShoppingCart\Application\DTO\AddItemInput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Tests\Util\InMemoryCartRepositoryMock;
use PHPUnit\Framework\TestCase;

class AddItemToCartUseCaseTest extends TestCase {

    public function testExecuteWithOneProduct(): void {
        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('1', 'Product 1'));
        
        $input = new AddItemInput('1', '1', 'Product 1', 2, 1500);
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository);
        $output = $addItemToCart->execute($input);

        $this->assertEquals(3000, $output->total);
        $this->assertCount(1, $output->items);
    }

    public function testExecuteWithMultipleProducts(): void {
        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->exactly(2))
            ->method('getById')
            ->willReturnCallback(function ($id) {
                return new Product($id, 'Product ' . substr($id, -1));
            });
        
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository);
        
        $input1 = new AddItemInput('2', '1', 'Product 1', 1, 1000);
        $addItemToCart->execute($input1);
        
        $input2 = new AddItemInput('2', '2', 'Product 2', 3, 2000);
        $output = $addItemToCart->execute($input2);

        $this->assertEquals(7000, $output->total);
        $this->assertCount(2, $output->items);
    }

    public function testExecutingWithZeroQuantityMustThrowInvalidArgumentException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');
        
        $cartRepository = new InMemoryCartRepositoryMock();
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('1', 'Product 1'));
        
        $input = new AddItemInput('3', '1', 'Product 1', 0, 1500);
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository);
        $addItemToCart->execute($input);
    }

    public function testExecuteShouldThrowLogicExceptionWhenTryToAddItemInACompletedCart(): void {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot modify a cart that is not opened');

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn(new CartBuilder()
                ->withId('nonexistent-cart-id')
                ->withStatus(CartStatus::COMPLETED)
                ->build()
            );
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('1', 'Product 1'));
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository);

        $addItemToCart->execute(new AddItemInput('1', '1', 'Product 1', 1, 1000));
    }
}

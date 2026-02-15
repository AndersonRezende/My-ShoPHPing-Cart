<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use DomainException;
use InvalidArgumentException;
use LogicException;
use MyShoppingCart\Application\DTO\AddItemToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class AddItemToCartUseCaseTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteShouldThrowDomainExceptionWhenUserIsNotAuthorized(): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Access denied: You can not modify this cart.');

        $cart = new CartBuilder()
            ->withId('c-1')
            ->withUserIds(['u-2'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
        $productRepository = $this->createMock(ProductRepository::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator->method('generate')->willReturn('item-1');

        $input = new AddItemToCartInput('c-1', 'u-1', 'p-1', 'Product 1', 2, 1500);
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository, $idGenerator);
        $addItemToCart->execute($input);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteWithOneProductAndAuthorizedUser(): void {
        $cart = new CartBuilder()
            ->withId('c-1')
            ->withUserIds(['u-1'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('p-1', 'Product 1'));
        
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator->method('generate')->willReturn('item-1');

        $input = new AddItemToCartInput('c-1', 'u-1', 'p-1', 'Product 1', 2, 1500);
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository, $idGenerator);
        $output = $addItemToCart->execute($input);

        $this->assertEquals(3000, $output->total);
        $this->assertCount(1, $output->items);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteWithMultipleProductsAndAuthorizedUser(): void {
        $cart = new CartBuilder()
            ->withId('c-1')
            ->withUserIds(['u-1'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->exactly(2))
            ->method('findById')
            ->willReturn($cart);
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->exactly(2))
            ->method('getById')
            ->willReturnCallback(function ($id) {
                return new Product($id, 'Product ' . substr($id, -1));
            });
        
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator->method('generate')->willReturnOnConsecutiveCalls('item-1', 'item-2');
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository, $idGenerator);
        
        $input1 = new AddItemToCartInput('c-2', 'u-1', '1', 'Product 1', 1, 1000);
        $addItemToCart->execute($input1);
        
        $input2 = new AddItemToCartInput('c-2', 'u-1', '2', 'Product 2', 3, 2000);
        $output = $addItemToCart->execute($input2);

        $this->assertEquals(7000, $output->total);
        $this->assertCount(2, $output->items);
    }

    public function testExecutingWithZeroQuantityMustThrowInvalidArgumentException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $cart = new CartBuilder()
            ->withId('c-1')
            ->withUserIds(['u-1'])
            ->build();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->once())
            ->method('findById')
            ->willReturn($cart);
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('p-1', 'Product 1'));
        $uuidGenerator = $this->createMock(IdGeneratorInterface::class);
        $uuidGenerator->expects($this->once())->method('generate')->willReturn('1');

        $input = new AddItemToCartInput('c-1', 'u-1', 'p-1', 'Product 1', 0, 1500);
        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository, $uuidGenerator);
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
                ->withUserIds(['u-1'])
                ->build()
            );
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('getById')
            ->willReturn(new Product('p-1', 'Product 1'));
        $uuidGenerator = $this->createMock(IdGeneratorInterface::class);
        $uuidGenerator->expects($this->once())->method('generate')->willReturn('1');

        $addItemToCart = new AddItemToCartUseCase($cartRepository, $productRepository, $uuidGenerator);

        $addItemToCart->execute(new AddItemToCartInput('c-1', 'u-1','1', 'Product 1', 1, 1000));
    }
}

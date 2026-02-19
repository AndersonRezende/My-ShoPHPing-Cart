<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\Cart;

use DomainException;
use MyShoppingCart\Application\DTO\RemoveItemFromCartInput;
use MyShoppingCart\Application\UseCase\Cart\RemoveItemFromCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class RemoveItemFromCartUseCaseTest extends DatabaseTestCase {

    public function testExecuteRemoveItemFromCart(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES ('1', 'Product 1');");
        $cart = new Cart('1');
        $cart->addItem(new CartItem('1', new Product('1', 'Product 1'), 1, new Money(1000)));
        $cartRepository = new CartRepositoryPdo($this->connection);
        $cartRepository->save($cart);
        
        $removeItemFromCart = new RemoveItemFromCartUseCase($cartRepository);
        $input = new RemoveItemFromCartInput('1', '1');
        $removeItemFromCart->execute($input);
        $cart = $cartRepository->findById('1');

        $this->assertNotNull($cart);
        $this->assertEmpty($cart->items());
    }

    public function testExecuteRemoveItemFromCartThrowsExceptionWhenCartNotFound(): void {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Cart not found');

        $cartRepository = new CartRepositoryPdo($this->connection);
        $removeItemFromCart = new RemoveItemFromCartUseCase($cartRepository);
        $input = new RemoveItemFromCartInput('non-existent-cart-id', '1');

        $removeItemFromCart->execute($input);
    }

    public function testExecuteRemoveItemFromCartThrowsExceptionWhenItemNotInCart(): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cart item not exists in the cart');

        $cart = new Cart('1');
        $cartRepository = new CartRepositoryPdo($this->connection);
        $cartRepository->save($cart);
        $removeItemFromCart = new RemoveItemFromCartUseCase($cartRepository);
        $input = new RemoveItemFromCartInput('1', 'non-existent-product-id');

        $removeItemFromCart->execute($input);
    }
}
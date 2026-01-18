<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase;

use MyShoppingCart\Application\DTO\UpdateItemQuantityInput;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;

class UpdateItemQuantityTest extends DatabaseTestCase {
    
    public function testShouldUpdateItemQuantity(): void {
        $this->connection->exec("INSERT INTO products (id, name) VALUES ('1', 'Product 1');");
        $cart = new Cart('1');
        $cart->addItem(new CartItem('1', new Product('1', 'Product 1'), 1, new Money(1000)));
        $cartRepository = new CartRepositoryPdo($this->connection);
        $cartRepository->save($cart);
        
        $updateItemQuantity = new UpdateItemQuantity($cartRepository);
        $input = new UpdateItemQuantityInput('1', '1', 3);
        $updateItemQuantity->execute($input);
        $updatedCartItem = $cartRepository->findById('1')->items()[0];
        
        $this->assertEquals(3, $updatedCartItem->quantity());
    }

    public function testShouldThrowExceptionWhenCartNotFound(): void {
        $cartRepository = new CartRepositoryPdo($this->connection);
        $updateItemQuantity = new UpdateItemQuantity($cartRepository);
        $input = new UpdateItemQuantityInput('non-existent-cart-id', '1', 3);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart not found');

        $updateItemQuantity->execute($input);
    }

    public function testShouldThrowExceptionWhenItemNotInCart(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cart item not exists in the cart');
        
        $cart = new Cart('1');
        $cartRepository = new CartRepositoryPdo($this->connection);
        $cartRepository->save($cart);
        $updateItemQuantity = new UpdateItemQuantity($cartRepository);
        $input = new UpdateItemQuantityInput('1', '1', 3);
        
        $updateItemQuantity->execute($input);
    }
}
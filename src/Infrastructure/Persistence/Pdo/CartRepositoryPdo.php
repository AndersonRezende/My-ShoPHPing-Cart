<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;
use PDO;

final class CartRepositoryPdo implements CartRepository {

    public function __construct(private PDO $pdo) {}

    public function get(): Cart {
        $cart = new Cart();

        $stmt = $this->pdo->query(
            'SELECT product_id, description, quantity, unit_price
             FROM cart_items WHERE cart_id = 1'
        );

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $product = new Product($row['product_id'], $row['description']);
            $cartItem = new CartItem(
                $product,
                (int) $row['quantity'],
                new Money((int) $row['unit_price'])
            );

            $cart->addItem($cartItem);
        }

        return $cart;
    }

    public function save(Cart $cart): void {
        $this->pdo->exec('DELETE FROM cart_items WHERE cart_id = 1');

        $stmt = $this->pdo->prepare(
            'INSERT INTO cart_items 
             (cart_id, product_id, description, quantity, unit_price)
             VALUES (1, :product_id, :description, :quantity, :unit_price)'
        );

        foreach ($cart->items() as $item) {
            $stmt->execute([
                'product_id' => null,
                'description' => $item->description ?? '',
                'quantity' => $item->quantity ?? 0,
                'unit_price' => $item->subtotal()->amount()
            ]);
        }
    }
}

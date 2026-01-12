<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Application\Repository\CartRepository;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;
use PDO;

final class CartRepositoryPdo implements CartRepository {

    public function __construct(private PDO $pdo) {}

    public function save(Cart $cart): void {

        
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO carts (id, status) 
                VALUES (:id, :status)
                ON CONFLICT(id) DO UPDATE SET status = :status'
            );
            $stmt->execute([
                'id' => $cart->id(),
                'status' => $cart->status()->value
            ]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO cart_items 
                (cart_id, product_id, quantity, unit_price)
                VALUES (:cart_id, :product_id, :quantity, :unit_price)'
            );

            foreach ($cart->items() as $item) {
                $stmt->execute([
                    'cart_id' => $cart->id(),
                    'product_id' => $item->product()->id(),
                    'quantity' => $item->quantity(),
                    'unit_price' => $item->unitPrice()->amount()
                ]);
            }
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        $this->pdo->commit();
    }

    public function findById(string $id): ?Cart {
        $stmt = $this->pdo->prepare(
            'SELECT c.id as c_id, c.status as c_status,
                    ci.id as ci_id, ci.cart_id as ci_cart_id, ci.product_id as ci_product_id, ci.quantity as ci_quantity, ci.unit_price as ci_unit_price,
                    p.id as p_id, p.name as p_name
            FROM carts as c 
            JOIN cart_items as ci ON c.id = ci.cart_id
            JOIN products as p ON ci.product_id = p.id
            WHERE c.id = :cart_id'
        );
        $stmt->execute(['cart_id' => $id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            $cartItems = [];
            foreach ($rows as $row) {
                $product = new Product(strval($row['p_id']), $row['p_name']);
                $cartItem = new CartItem(
                    strval($row['ci_id']),
                    $product,
                    (int) $row['ci_quantity'],
                    new Money((int) $row['ci_unit_price'])
                );
                $cartItems[] = $cartItem;
            }

            return (new CartBuilder())
                ->withId($id)
                ->withStatus(CartStatus::from($rows[0]['c_status']))
                ->withCartItems($cartItems)
                ->build();
        }
        
        return null;
    }
}

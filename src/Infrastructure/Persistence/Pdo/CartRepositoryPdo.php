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
        $inTransaction = $this->pdo->inTransaction();
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }

        try {
            $this->upsertCart($cart);

            $dbProductIds = $this->getProductsIdsFromCartItemsByCartId($cart->id());
            $cartItemsProductIds = array_map(
                fn(CartItem $item) => $item->product()->id(),
                $cart->items()
            );

            $productsToDelete = array_diff($dbProductIds, $cartItemsProductIds);
            if (!empty($productsToDelete)) {
                $this->removeItemCartsByProductIds($cart->id(), $productsToDelete);
            }

            $this->upsertCartItems($cart);
            
            if (!$inTransaction) {
                $this->pdo->commit();
            }
        } catch (\PDOException $e) {
            if (!$inTransaction) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function upsertCart(Cart $cart): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO carts (id, status)
             VALUES (:id, :status)
             ON CONFLICT(id) DO UPDATE SET status = :status'
        );
        $stmt->execute(['id' => $cart->id(), 'status' => $cart->status()->value]);
    }

    private function getProductsIdsFromCartItemsByCartId(string $cartId): array {
        $stmt = $this->pdo->prepare('SELECT product_id FROM cart_items WHERE cart_id = :cart_id');
        $stmt->execute(['cart_id' => $cartId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function removeItemCartsByProductIds(string $cartId, array $productIds): void {
        $inQuery = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id IN ($inQuery)");
        $params = array_merge([$cartId], $productIds);
        $stmt->execute($params);
    }

    private function upsertCartItems(Cart $cart): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO cart_items 
            (cart_id, product_id, quantity, unit_price)
            VALUES (:cart_id, :product_id, :quantity, :unit_price)
            ON CONFLICT(cart_id, product_id) DO UPDATE SET 
                quantity = :quantity,
                unit_price = :unit_price'
        );

        foreach ($cart->items() as $item) {
            $stmt->execute([
                'cart_id' => $cart->id(),
                'product_id' => $item->product()->id(),
                'quantity' => $item->quantity(),
                'unit_price' => $item->unitPrice()->amount()
            ]);
        }
    }

    public function findById(string $id): ?Cart {
        $stmt = $this->pdo->prepare(
            'SELECT c.id as c_id, c.status as c_status,
                    ci.id as ci_id, ci.cart_id as ci_cart_id, ci.product_id as ci_product_id, ci.quantity as ci_quantity, ci.unit_price as ci_unit_price,
                    p.id as p_id, p.name as p_name
            FROM carts as c 
            INNER JOIN cart_items as ci ON c.id = ci.cart_id
            INNER JOIN products as p ON ci.product_id = p.id
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

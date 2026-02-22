<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\ValueObject\Money;
use PDO;

final readonly class CartRepositoryPdo implements CartRepository {

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
            $this->upsertCartUsers($cart);
            
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
             ON DUPLICATE KEY UPDATE status = :status'
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
            (id, cart_id, product_id, quantity, unit_price)
            VALUES (:id, :cart_id, :product_id, :quantity, :unit_price)
            ON DUPLICATE KEY UPDATE 
                quantity = :quantity,
                unit_price = :unit_price'
        );

        foreach ($cart->items() as $item) {
            $stmt->execute([
                'id' => $item->id(),
                'cart_id' => $cart->id(),
                'product_id' => $item->product()->id(),
                'quantity' => $item->quantity(),
                'unit_price' => $item->unitPrice()->amount()
            ]);
        }
    }

    private function upsertCartUsers(Cart $cart): void {
        $stmt = $this->pdo->prepare("DELETE FROM cart_users WHERE cart_id = :cart_id");
        $stmt->execute(['cart_id' => $cart->id()]);

        $stmt = $this->pdo->prepare("INSERT INTO cart_users (cart_id, user_id) VALUES (:cart_id, :user_id)");
        foreach ($cart->userIds() as $userId) {
            $stmt->execute([
                'cart_id' => $cart->id(),
                'user_id' => $userId
            ]);
        }
    }

    /** @throws ResourceNotFoundException */
    public function findById(string $id): Cart {
        $stmt = $this->pdo->prepare('SELECT id, status FROM carts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $cartData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartData) {
            throw new ResourceNotFoundException("Cart not found with ID {$id}.");
        }
        
        $cartItems = $this->findCartItemsByCartId($id);
        $userIds = $this->findCartUsersByCartId($id);
        
        return new CartBuilder()
            ->withId($cartData['id'])
            ->withStatus(CartStatus::from($cartData['status']))
            ->withCartItems($cartItems)
            ->withUserIds($userIds)
            ->build();
    }

    private function findCartItemsByCartId(string $cartId): array {
        $stmt = $this->pdo->prepare(
            'SELECT ci.id as ci_id, ci.quantity, ci.unit_price, 
                    p.id as p_id, p.name as p_name
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = :cart_id'
        );
        $stmt->execute(['cart_id' => $cartId]);
        $itemsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cartItems = [];
        foreach ($itemsData as $row) {
            $product = new Product($row['p_id'], $row['p_name']);
            $cartItems[] = new CartItem(
                strval($row['ci_id']),
                $product,
                (int)$row['quantity'],
                new Money((int)$row['unit_price'])
            );
        }
        return $cartItems;
    }

    private function findCartUsersByCartId(string $id): array {
        $stmt = $this->pdo->prepare('SELECT user_id FROM cart_users WHERE cart_id = :cart_id');
        $stmt->execute(['cart_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

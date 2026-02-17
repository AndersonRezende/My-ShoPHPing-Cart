<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;
use PDO;

final readonly class ProductRepositoryPdo implements ProductRepository {
    public function __construct(private PDO $pdo) {}

    public function search(string $term): array {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, category_id FROM products WHERE name LIKE :term'
        );

        $stmt->execute(['term' => "%{$term}%"]);

        return array_map(
            fn ($row) => new Product($row['id'], $row['name'], $row['category_id']),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function getById(string $id): Product {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new \RuntimeException("Product with ID {$id} not found.");
        }

        return new Product($row['id'], $row['name'], $row['category_id']);   
    }

    public function save(Product $product): Product {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (id, name, category_id) VALUES (:id, :name, :category_id)
             ON CONFLICT(id) DO UPDATE SET name = :name, category_id = :category_id'
        );

        $result = $stmt->execute([
            'id' => $product->id(),
            'name' => $product->name(),
            'category_id' => $product->categoryId()
        ]);

        if (!$result) {
            throw new \RuntimeException('Failed to save product.');
        }

        return $product;
    }

    /** @return Product[] */
    public function findAll(): array {
        $stmt = $this->pdo->prepare('SELECT * FROM products');
        $stmt->execute();
        return array_map(
            fn ($row) => new Product($row['id'], $row['name'], $row['category_id']),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function deleteById(string $id): void {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $result = $stmt->execute(['id' => $id]);
        if ($result === false) {
            throw new \RuntimeException("Product with ID {$id} not found.");
        }
    }

    /** @return Product[] */
    public function findByName(string $name): array {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE name LIKE :name');
        $stmt->execute(['name' => "%{$name}%"]);
        return array_map(
            fn ($row) => new Product($row['id'], $row['name'], $row['category_id']),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}

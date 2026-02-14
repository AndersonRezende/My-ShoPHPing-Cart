<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Repository\ProductRepository;
use PDO;

final readonly class ProductRepositoryPdo implements ProductRepository {
    public function __construct(private PDO $pdo) {}

    public function search(string $term): array {
        $stmt = $this->pdo->prepare(
            'SELECT id, name FROM products WHERE name LIKE :term'
        );

        $stmt->execute(['term' => "%{$term}%"]);

        return array_map(
            fn ($row) => new Product($row['id'], $row['name']),
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

        return new Product($row['id'], $row['name']);   
    }

    public function save(Product $product): Product {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (id, name) VALUES (:id, :name)
             ON CONFLICT(id) DO UPDATE SET name = :name'
        );

        $result = $stmt->execute([
            'id' => $product->id(),
            'name' => $product->name(),
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
            fn ($row) => new Product($row['id'], $row['name']),
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
}

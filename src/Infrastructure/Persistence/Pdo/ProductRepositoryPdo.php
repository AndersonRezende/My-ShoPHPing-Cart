<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Application\Repository\ProductRepository;
use MyShoppingCart\Domain\Entity\Product;
use PDO;

final class ProductRepositoryPdo implements ProductRepository {
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
}
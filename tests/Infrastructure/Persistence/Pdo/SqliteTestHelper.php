<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use PDO;

class SqliteTestHelper {

    public static function createConnection(): PDO {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::createSchema($pdo);

        return $pdo;
    }

    private static function createSchema(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE products (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL
            );
        ");

        $pdo->exec("
            CREATE TABLE cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cart_id INTEGER NOT NULL,
                product_id TEXT,
                description TEXT,
                quantity INTEGER NOT NULL,
                unit_price INTEGER NOT NULL
            );
        ");
    }
}
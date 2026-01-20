<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

/**
 * @codeCoverageIgnore
 */
final class PdoConnection {
    
    public static function getConnection(): \PDO {
        return new \PDO(
            'sqlite:' . __DIR__ . '/../../../../database/database.db.sqlite3',
            null,
            null,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
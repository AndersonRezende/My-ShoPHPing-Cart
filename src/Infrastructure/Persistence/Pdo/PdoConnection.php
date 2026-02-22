<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

/**
 * @codeCoverageIgnore
 */
final class PdoConnection {
    
    public static function getConnection(): \PDO {
        $dbUrl = getenv('DATABASE_URL');

        if ($dbUrl) {
            $parts = parse_url($dbUrl);
            
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $parts['scheme'],
                $parts['host'],
                $parts['port'] ?? '3306',
                ltrim($parts['path'], '/')
            );
            
            return new \PDO(
                $dsn,
                $parts['user'] ?? null,
                $parts['pass'] ?? null,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
        }

        // Fallback para SQLite local
        return new \PDO(
            'sqlite:' . __DIR__ . '/../../../../database/database.db',
            null,
            null,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }
}

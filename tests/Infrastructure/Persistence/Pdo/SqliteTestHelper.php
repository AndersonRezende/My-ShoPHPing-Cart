<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use PDO;

class SqliteTestHelper {

    public static function createConnection(): PDO {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::runMigrations($pdo);

        return $pdo;
    }

    private static function runMigrations(PDO $pdo): void {
        $adapter = new \Phinx\Db\Adapter\SQLiteAdapter(['name' => ':memory:']);
        $adapter->setConnection($pdo);

        $migrationDir = dirname(__DIR__, 4) . '/database/migrations';
        $files = glob($migrationDir . '/*.php');
        sort($files);
        foreach ($files as $file) {
            require_once $file;
            $base = basename($file, '.php');
            $parts = explode('_', $base);
            $version = array_shift($parts);
            $className = implode('', array_map('ucfirst', $parts));
            $migration = new $className('testing', (int)$version);
            $migration->setAdapter($adapter);
            $migration->change();
        }
    }
}
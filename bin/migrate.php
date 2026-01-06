<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MyShoppingCart\Infrastructure\Persistence\Pdo\PdoConnection;

$pdo = PdoConnection::getConnection();

$migrationsPath = __DIR__ . '/../database/migrations/';

$files = scandir($migrationsPath);

foreach ($files as $file) {
    if (!str_ends_with($file, '.sql')) {
        continue;
    }

    echo "Running migration: {$file}\n";
    $sql = file_get_contents($migrationsPath . $file);
    $pdo->exec($sql);
}

echo "Migration completed.\n";
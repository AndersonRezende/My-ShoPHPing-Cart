<?php

require __DIR__ . '/config/bootstrap.php';

// Ler variáveis de ambiente com fallbacks seguros
$dbAdapter = $_ENV['DB_CONNECTION'] ?? 'mysql';
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? 'shopping_cart';
$dbUser = $_ENV['DB_USER'] ?? 'root';
$dbPass = $_ENV['DB_PASS'] ?? '';
$dbPort = $_ENV['DB_PORT'] ?? '3306';

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',

        'development' => [
            'adapter' => $dbAdapter,
            'host' => $dbHost,
            'name' => $dbName,
            'user' => $dbUser,
            'pass' => $dbPass,
            'port' => $dbPort,
            'charset' => 'utf8',
        ],
        
        'sqlite_testing' => [
            'adapter' => 'sqlite',
            'name' => ':memory:',
            'memory' => true,
        ],

        'mysql_testing' => [
            'adapter' => 'mysql',
            'host' => $dbHost,
            'name' => $dbName . '_test',
            'user' => $dbUser,
            'pass' => $dbPass,
            'port' => $dbPort,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];

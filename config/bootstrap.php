<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis de ambiente do .env
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Configuração do Container de Injeção de Dependência (PHP-DI)
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/dependencies.php');

return $containerBuilder->build();

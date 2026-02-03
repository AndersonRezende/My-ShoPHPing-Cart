<?php declare(strict_types=1);

use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Infrastructure\Persistence\Pdo\PdoConnection;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Infrastructure\Persistence\Pdo\UserRepositoryPdo;
use Psr\Container\ContainerInterface;
use function DI\autowire;

return [
    // 1. Configuração do PDO
    // Ensinamos ao container como criar a instância do PDO usando sua classe de conexão existente.
    PDO::class => function (ContainerInterface $c) {
        return PdoConnection::getConnection();
    },

    // 2. Bind de Interfaces para Implementações
    // Quando um Use Case pedir CartRepository, o container injetará CartRepositoryPdo.
    // O PHP-DI resolverá automaticamente a dependência de PDO no construtor do repositório.
    CartRepository::class => autowire(CartRepositoryPdo::class),
    ProductRepository::class => autowire(ProductRepositoryPdo::class),
    UserRepository::class => autowire(UserRepositoryPdo::class),
];

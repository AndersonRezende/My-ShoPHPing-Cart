<?php declare(strict_types=1);

use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Repository\ProductRepository;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use MyShoppingCart\Domain\Service\TokenGeneratorInterface;
use MyShoppingCart\Infrastructure\Persistence\Pdo\CartRepositoryPdo;
use MyShoppingCart\Infrastructure\Persistence\Pdo\PdoConnection;
use MyShoppingCart\Infrastructure\Persistence\Pdo\ProductRepositoryPdo;
use MyShoppingCart\Infrastructure\Persistence\Pdo\UserRepositoryPdo;
use MyShoppingCart\Infrastructure\Service\JwtTokenGenerator;
use MyShoppingCart\Infrastructure\Service\UuidGenerator;
use Psr\Container\ContainerInterface;
use function DI\autowire;
use function DI\get;

return [
    // Configurações Globais
    'jwt.secret' => fn() => $_ENV['JWT_SECRET'] ?? 'default_secret_key_change_me',

    // 1. Configuração do PDO
    PDO::class => function (ContainerInterface $c) {
        return PdoConnection::getConnection();
    },

    // 2. Bind de Interfaces para Implementações
    // Quando um Use Case pedir CartRepository, o container injetará CartRepositoryPdo.
    // O PHP-DI resolverá automaticamente a dependência de PDO no construtor do repositório.
    CartRepository::class => autowire(CartRepositoryPdo::class),
    ProductRepository::class => autowire(ProductRepositoryPdo::class),
    UserRepository::class => autowire(UserRepositoryPdo::class),

    // 3. Bind de Serviços de Domínio
    TokenGeneratorInterface::class => autowire(JwtTokenGenerator::class)
        ->constructorParameter('secretKey', get('jwt.secret')),
    IdGeneratorInterface::class => autowire(UuidGenerator::class),
];

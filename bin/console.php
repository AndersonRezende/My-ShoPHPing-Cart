#!/usr/bin/env php
<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\AddItemToCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\ShowCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CheckoutCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CreateCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\AssociateUserToCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\CreateProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ListProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ShowProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\UpdateProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\DeleteProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\User\RegisterUserCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\User\LoginUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configuração do Container (Compartilhado com a Web se possível)
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

// 2. Aplicação Console
$application = new Application('My Shopping Cart CLI', '1.0.0');

// 3. Registrar Comandos
$commandLoader = new ContainerCommandLoader($container, [
    'msp:show-product' => ShowProductCommand::class,
    'msp:list-products' => ListProductCommand::class,
    'msp:create-product' => CreateProductCommand::class,
    'msp:update-product' => UpdateProductCommand::class,
    'msp:delete-product' => DeleteProductCommand::class,

    'msp:show-cart' => ShowCartCommand::class,
    'msp:checkout' => CheckoutCartCommand::class,
    'msp:create-cart' => CreateCartCommand::class,
    'msp:add-item-to-cart' => AddItemToCartCommand::class,
    'msp:associate-user-to-cart' => AssociateUserToCartCommand::class,

    'msp:register-user' => RegisterUserCommand::class,
    'msp:login-user' => LoginUserCommand::class,
]);
$application->setCommandLoader($commandLoader);

$application->run();

#!/usr/bin/env php
<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\AddItemToCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CheckoutCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CreateCartCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\CreateProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\ListProductsCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\ShowProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\UpdateProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\DeleteProductCommand;
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
// Aqui você registraria seus comandos. Exemplo:
// $commandLoader = new ContainerCommandLoader($container, [
//     'app:meu-comando' => MeuComando::class
// ]);
// $application->setCommandLoader($commandLoader);
$commandLoader = new ContainerCommandLoader($container, [
    'msp:show-product' => ShowProductCommand::class,
    'msp:list-products' => ListProductsCommand::class,
    'msp:create-product' => CreateProductCommand::class,
    'msp:update-product' => UpdateProductCommand::class,
    'msp:delete-product' => DeleteProductCommand::class,

    'msp:checkout' => CheckoutCommand::class,
    'msp:create-cart' => CreateCartCommand::class,
    'msp:add-item-to-cart' => AddItemToCartCommand::class,
]);
$application->setCommandLoader($commandLoader);

$application->run();
<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..');
$dotenv->load();

// 1. Configuração do Container de Injeção de Dependência (PHP-DI)
$containerBuilder = new ContainerBuilder();

// Aqui você poderá adicionar definições, ex: interfaces para implementações
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');

$container = $containerBuilder->build();

// 2. Criação da App Slim usando o Container
AppFactory::setContainer($container);
$app = AppFactory::create();

// 3. Middlewares
// Adiciona middleware de roteamento
$app->addRoutingMiddleware();

// Adiciona middleware do Twig para renderização de HTML
// Certifique-se de criar a pasta 'templates' na raiz ou ajustar o caminho
$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->addErrorMiddleware(true, true, true);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();
<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use MyShoppingCart\Infrastructure\Http\Controller\ListProductsController;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

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

// 4. Rotas (Idealmente, mova isso para um arquivo separado config/routes.php)
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Bem-vindo ao My Shopping Cart (Clean Arch)");
    return $response;
});

// Rota para listar produtos usando o Controller
$app->get('/products', ListProductsController::class);

$app->run();
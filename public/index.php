<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

// Bootstrap da aplicação (Autoload, Dotenv, Container)
$container = require __DIR__ . '/../config/bootstrap.php';

// 2. Criação da App Slim usando o Container
AppFactory::setContainer($container);
$app = AppFactory::create();

// 3. Middlewares
$app->addRoutingMiddleware();

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->addErrorMiddleware(true, true, true);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();

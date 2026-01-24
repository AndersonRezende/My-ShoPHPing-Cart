<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Http\Controller\ListProductsController;
use Slim\App;

return function (App $app) {
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write("Bem-vindo ao My Shopping Cart (Clean Arch)");
        return $response;
    });

    $app->get('/products', ListProductsController::class);
};

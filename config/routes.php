<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Http\Controller\ShowProductController;
use MyShoppingCart\Infrastructure\Http\Controller\ListProductsController;
use MyShoppingCart\Infrastructure\Http\Controller\CreateProductController;
use MyShoppingCart\Infrastructure\Http\Controller\UpdateProductController;
use MyShoppingCart\Infrastructure\Http\Controller\DeleteProductController;
use Slim\App;

return function (App $app) {
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write("Bem-vindo ao My Shopping Cart (Clean Arch)");
        return $response;
    });

    $app->get('/products/{id}', ShowProductController::class);
    $app->get('/products', ListProductsController::class);
    $app->post('/products', CreateProductController::class);
    $app->put('/products/{id}', UpdateProductController::class);
    $app->delete('/products/{id}', DeleteProductController::class);
};

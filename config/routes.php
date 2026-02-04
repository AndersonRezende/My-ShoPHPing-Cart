<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Http\Controller\Product\ShowProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\ListProductsController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\CreateProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\UpdateProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\DeleteProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\CreateCartController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\AddItemToCartController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\CheckoutCartController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\ShowCartController;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\AssociateUserToCartController;
use MyShoppingCart\Infrastructure\Http\Controller\User\RegisterUserController;
use MyShoppingCart\Infrastructure\Http\Controller\User\LoginUserController;
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

    // Rotas do Carrinho
    $app->get('/carts/{id}', ShowCartController::class);
    $app->post('/carts', CreateCartController::class);
    $app->post('/carts/{id}/items', AddItemToCartController::class);
    $app->post('/carts/{id}/checkout', CheckoutCartController::class);
    $app->post('/carts/{id}/associate-user', AssociateUserToCartController::class);

    // Rotas de Usuário
    $app->post('/users/register', RegisterUserController::class);
    $app->post('/users/login', LoginUserController::class);
};

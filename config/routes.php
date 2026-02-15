<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Http\Controller\Product\ShowProductController;
use MyShoppingCart\Infrastructure\Http\Controller\Product\ListProductController;
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
use MyShoppingCart\Infrastructure\Http\Middleware\AuthMiddleware;
use Slim\App;

return function (App $app) {
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write("Bem-vindo ao My Shopping Cart (Clean Arch)");
        return $response;
    });

    // Rotas de Usuário
    $app->post('/users/register', RegisterUserController::class);
    $app->post('/users/login', LoginUserController::class);

    // --- ROTAS PRIVADAS (Protegidas pelo JWT) ---
    $app->group('/msc', function ($group) {

        // Rotas de Produto
        $group->get('/products/{id}', ShowProductController::class);
        $group->get('/products', ListProductController::class);
        $group->post('/products', CreateProductController::class);
        $group->put('/products/{id}', UpdateProductController::class);
        $group->delete('/products/{id}', DeleteProductController::class);

        // Rotas do Carrinho
        $group->get('/carts/{id}', ShowCartController::class);
        $group->post('/carts', CreateCartController::class);
        $group->post('/carts/{id}/items', AddItemToCartController::class);
        $group->post('/carts/{id}/checkout', CheckoutCartController::class);
        $group->post('/carts/{id}/associate-user', AssociateUserToCartController::class);

        $group->get('/home', function ($request, $response, $args) {
            $response->getBody()->write("Bem-vindo ao My Shopping Cart (Clean Arch)");
            return $response;
        });
    })->add(new AuthMiddleware());
};

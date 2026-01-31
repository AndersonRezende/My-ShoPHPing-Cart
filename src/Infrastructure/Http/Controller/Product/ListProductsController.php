<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\UseCase\ListProductsUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListProductsController {
    
    public function __construct(private ListProductsUseCase $listProductsUseCase) {}

    public function __invoke(Request $request, Response $response): Response {
        $products = $this->listProductsUseCase->execute();

        $data = array_map(fn($product) => [
            'id' => $product->id(),
            'name' => $product->name(),
        ], $products);

        $payload = json_encode($data, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
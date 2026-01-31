<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\DTO\AddItemInput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddItemToCartController {
    
    public function __construct(private AddItemToCartUseCase $addItemToCart) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $cartId = $args['id'] ?? '';
        $body = json_decode((string) $request->getBody(), true);

        if (empty($cartId) || empty($body['product_id']) || empty($body['quantity'])) {
             $response->getBody()->write(json_encode(['error' => 'Invalid input']));
             return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $input = new AddItemInput(
            $cartId,
            $body['product_id'],
            $body['description'] ?? '',
            (int) $body['quantity'],
            (int) ($body['unit_price'] ?? 0)
        );

        $this->addItemToCart->execute($input);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use Exception;
use MyShoppingCart\Application\DTO\AddItemToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class AddItemToCartController {
    
    public function __construct(private AddItemToCartUseCase $addItemToCart) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $cartId = $args['id'] ?? '';
        $userId = $request->getAttribute('userId');
        $body = json_decode((string) $request->getBody(), true);

        if (empty($cartId) || empty($body['product_id']) || empty($body['quantity'])) {
             $response->getBody()->write(json_encode(['error' => 'Invalid input']));
             return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $input = new AddItemToCartInput(
            $cartId,
            $userId,
            $body['product_id'],
            $body['description'] ?? '',
            (int) $body['quantity'],
            (int) ($body['unit_price'] ?? 0)
        );

        try {
            $this->addItemToCart->execute($input);
        } catch (Exception $e) {
            $payload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}

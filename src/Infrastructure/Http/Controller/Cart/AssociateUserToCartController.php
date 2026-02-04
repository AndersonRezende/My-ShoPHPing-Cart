<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AssociateUserToCartController {
    public function __construct(private AssociateUserToCartUseCase $associateUserToCartUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $cartId = $args['id'];
        $userId = $data['userId'];

        try {
            $this->associateUserToCartUseCase->execute($cartId, $userId);
            return $response->withStatus(204);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\DTO\AssociateUserToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class AssociateUserToCartController {
    public function __construct(private AssociateUserToCartUseCase $associateUserToCartUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $data = json_decode((string) $request->getBody(), true);
        $cartId = $args['id'];
        $userCartOwner = $request->getAttribute('userId');
        $input = new AssociateUserToCartInput($cartId, $userCartOwner, $data['user_id']);

        try {
            $this->associateUserToCartUseCase->execute($input);
            return $response->withStatus(204);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}

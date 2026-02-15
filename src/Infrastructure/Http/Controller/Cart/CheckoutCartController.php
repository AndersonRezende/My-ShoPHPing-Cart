<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use LogicException;
use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use MyShoppingCart\Domain\Enum\CartStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class CheckoutCartController {
    
    public function __construct(private CheckoutCartUseCase $checkoutUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $cartId = $args['id'] ?? '';
        $body = json_decode((string) $request->getBody(), true);
        
        $input = new CheckoutInput($cartId, $body['userId']);
        try {
            $cart = $this->checkoutUseCase->execute($input);
        } catch (LogicException $exception) {
            $errorPayload = json_encode(['error' => $exception->getMessage()]);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $payload = json_encode(['id' => $cart->id(), 'status' => CartStatus::COMPLETED->value, 'total' => $cart->total()]);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}

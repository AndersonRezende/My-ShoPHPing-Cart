<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller;

use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\UseCase\CheckoutUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CheckoutController {
    
    public function __construct(private CheckoutUseCase $checkoutUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $cartId = $args['id'] ?? '';
        
        $input = new CheckoutInput($cartId);
        $cart = $this->checkoutUseCase->execute($input);

        $payload = json_encode(['id' => $cart->id(), 'status' => 'COMPLETED', 'total' => $cart->total()]);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}

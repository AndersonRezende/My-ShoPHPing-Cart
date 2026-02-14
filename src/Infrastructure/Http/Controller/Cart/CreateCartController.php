<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Application\UseCase\Cart\CreateCartUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class CreateCartController {
    
    public function __construct(private CreateCartUseCase $createCartUseCase) {}

    public function __invoke(Request $request, Response $response): Response {
        $input = new CreateCartInput();
        $cart = $this->createCartUseCase->execute($input);

        $payload = json_encode(['id' => $cart->id(), 'status' => 'OPEN']);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}

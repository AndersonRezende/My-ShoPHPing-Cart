<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateProductController {
    
    public function __construct(private readonly CreateProductUseCase $createProductUseCase) {}

    public function __invoke(Request $request, Response $response): Response {
        $body = json_decode((string) $request->getBody(), true);
        
        if (!$this->validateInput($body)) {
            $errorPayload = json_encode(['error' => 'Invalid input: name is required.']);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $input = new CreateProductInput($body['name']);
        $product = $this->createProductUseCase->execute($input);

        $payload = json_encode(['id' => $product->id(), 'name' => $product->name()]);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    private function validateInput(array $data): bool {
        return isset($data['name']) && is_string($data['name']) && trim($data['name']) !== '';
    }
}
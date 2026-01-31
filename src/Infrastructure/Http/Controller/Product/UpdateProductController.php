<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\DTO\UpdateProductInput;
use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateProductController {
    
    public function __construct(private UpdateProductUseCase $updateProductUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = $args['id'] ?? null;
        $body = json_decode((string) $request->getBody(), true);
        
        if (!$this->validateInput($id, $body)) {
            $errorPayload = json_encode(['error' => 'Invalid input: ID and name are required.']);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $input = new UpdateProductInput($id, $body['name']);

        try {
            $product = $this->updateProductUseCase->execute($input);
        } catch (\RuntimeException $e) {
            $errorPayload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $payload = json_encode(['id' => $product->id(), 'name' => $product->name()]);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function validateInput(?string $id, ?array $data): bool {
        return !empty($id) && isset($data['name']) && is_string($data['name']) && trim($data['name']) !== '';
    }
}

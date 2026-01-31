<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Product;

use MyShoppingCart\Application\DTO\ShowProductInput;
use MyShoppingCart\Application\UseCase\Product\ShowProductUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShowProductController {
    
    public function __construct(private ShowProductUseCase $showProductUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = $args['id'] ?? null;
        
        if (!$this->validateInput($id)) {
            $errorPayload = json_encode(['error' => 'Invalid input: ID and name are required.']);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $input = new ShowProductInput($id);

        try {
            $product = $this->showProductUseCase->execute($input);
        } catch (\RuntimeException $e) {
            $errorPayload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $payload = json_encode(['id' => $product->id(), 'name' => $product->name()]);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function validateInput(?string $id): bool {
        return is_numeric($id);
    }
}

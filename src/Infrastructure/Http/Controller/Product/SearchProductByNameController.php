<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Product;

use Exception;
use MyShoppingCart\Application\DTO\SearchProductByNameInput;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MyShoppingCart\Application\UseCase\Product\SearchProductByNameUseCase;

class SearchProductByNameController {

    public function __construct(private SearchProductByNameUseCase $searchProductByNameUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $name = $args['name'] ?? null;

        try {
            $products = $this->searchProductByNameUseCase->execute(new SearchProductByNameInput($name));

            $data = array_map(fn($product) => [
                'id' => $product->id(),
                'name' => $product->name(),
            ], $products);
        } catch (Exception $e) {
            $errorPayload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($errorPayload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $payload = json_encode($data, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

}
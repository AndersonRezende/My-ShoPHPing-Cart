<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\Cart;

use InvalidArgumentException;
use MyShoppingCart\Application\DTO\ShowCartInput;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Entity\CartItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShowCartController {

    public function __construct(private ShowCartUseCase $showCartUseCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        $showCartInput = new ShowCartInput($args['id']);
        try {
            $cart = $this->showCartUseCase->execute($showCartInput);
        } catch (InvalidArgumentException $e) {
            $payload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $payload = json_encode($this->formatPayload($cart));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function formatPayload(Cart $cart): array {
        $payload = [
            'cart' => [
                'id' => $cart->id(),
                'status' => $cart->status(),
                'items' => $this->formatItems($cart)
            ]
        ];
        return $payload;
    }

    private function formatItems(Cart $cart): array {
        $formattedItems = [];
        /** @var CartItem $item */
        foreach ($cart->items() as $item) {
            $formattedItems[] = [
                'productId' => $item->product()->id(),
                'name' => $item->product()->name(),
                'quantity' => $item->quantity(),
                'price' => $item->unitPrice()->amount(),
                'subTotal' => $item->subTotal()->amount()
            ];
        }
        return $formattedItems;
    }
}
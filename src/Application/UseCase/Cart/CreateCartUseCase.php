<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\Repository\CartRepository;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;

readonly class CreateCartUseCase {
    public function __construct(
        private CartRepository $cartRepository,
        private IdGeneratorInterface $idGenerator
    ) {}

    public function execute(CreateCartInput $input): Cart {
        $id = $this->idGenerator->generate();
        $cart = new CartBuilder()
            ->withId($id)
            ->withUserIds([$input->owner])
            ->withStatus(CartStatus::OPENED)
            ->build();
        $this->cartRepository->save($cart);
        return $cart;
    }
}

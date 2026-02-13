<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
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
        $cart = new Cart($id);
        $this->cartRepository->save($cart);
        return $cart;
    }
}

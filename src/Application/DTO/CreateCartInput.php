<?php declare(strict_types=1);

namespace MyShoppingCart\Application\DTO;

readonly class CreateCartInput {
    public function __construct(public string $owner) {}
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Service;

interface IdGeneratorInterface {
    public function generate(): string;
}

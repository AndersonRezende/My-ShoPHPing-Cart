<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Service;

use Firebase\JWT\JWT;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Service\TokenGeneratorInterface;

readonly class JwtTokenGenerator implements TokenGeneratorInterface {
    public function __construct(private string $secretKey) {}

    public function generate(User $user): string {
        $payload = [
            'iss' => 'my-shopping-cart-api',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user->id(),
            'email' => $user->email()->value()
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Infrastructure\Service\JwtTokenGenerator;
use MyShoppingCart\Domain\Entity\User;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class JwtTokenGeneratorTest extends TestCase {
    private const string JWT_SECRET = 'my_super_secure_test_jwt_secret_123456';

    #[AllowMockObjectsWithoutExpectations]
    public function testGenerateReturnsValidJwtWithExpectedClaims(): void {
        $user = $this->createMock(User::class);
        $user->method('id')->willReturn('user-123');
        $email = $this->createMock(Email::class);
        $email->method('value')->willReturn('user@test.com');
        $user->method('email')->willReturn($email);

        $generator = new JwtTokenGenerator(self::JWT_SECRET);
        $token = $generator->generate($user);
        $decoded = JWT::decode($token, new Key(self::JWT_SECRET, 'HS256'));

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals('my-shopping-cart-api', $decoded->iss);
        $this->assertEquals('user-123', $decoded->sub);
        $this->assertEquals('user@test.com', $decoded->email);
        $this->assertGreaterThan(time(), $decoded->exp);
        $this->assertLessThanOrEqual(time() + 3600, $decoded->exp);
    }
}
<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;

use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    public function testCreateUser(): void {
        $user = new User(
            '123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );

        $this->assertEquals('123', $user->id());
        $this->assertEquals('Anderson', $user->name());
        $this->assertEquals('anderson@example.com', $user->email()->value());
        $this->assertTrue($user->password()->verify('password123'));
    }
}

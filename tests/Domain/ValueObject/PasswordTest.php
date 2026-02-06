<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\ValueObject;

use InvalidArgumentException;
use MyShoppingCart\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase {

    public function testCreatePassword(): void {
        $password = '123456';

        $passwordVO = new Password($password);

        $this->assertEquals($password, $passwordVO->value());
    }

    public function testHashPassword(): void {
        $password = '123456';

        $passwordVO = Password::hash($password);

        $this->assertNotEquals($password, $passwordVO->value());
    }

    public function testInvalidPassword(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 6 characters long');

        $password = '12345';

        new Password($password);
    }
}

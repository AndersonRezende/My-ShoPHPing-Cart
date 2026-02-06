<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\ValueObject;

use MyShoppingCart\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase {

    public function testCreateEmail(): void {
        $email = 'test@example.com';

        $emailVO = new Email($email);

        $this->assertEquals($email, $emailVO->value());
        $this->assertEquals($email, $emailVO);
    }

    public function testCreateInvalidEmail(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        $email = 'test@example';

        new Email($email);
    }
}

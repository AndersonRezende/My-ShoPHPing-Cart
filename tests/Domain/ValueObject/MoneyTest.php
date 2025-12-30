<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\ValueObject;

use MyShoppingCart\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase {

    public function testCannotCreateMoneyWithNegativeAmount(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');
        new Money(-100);
    }

    public function testCanCreateMoneyWithZeroAmount(): void {
        $money = new Money(0);
        $this->assertInstanceOf(Money::class, $money);
    }

    public function testCanCreateMoneyWithPositiveAmount(): void {
        $money = new Money(1500);
        $this->assertInstanceOf(Money::class, $money);
    }

    public function testGetAmountReturnsCorrectValue(): void {
        $amount = 2500;
        $money = new Money($amount);
        $this->assertEquals($amount, $money->amount());
    }

    public function testCanAddMoney(): void {
        $money1 = new Money(1000);
        $money2 = new Money(500);
        $result = $money1->add($money2);
        $this->assertEquals(1500, $result->amount());
    }

    public function testCannotMultiplyMoneyWithNegativeFactor(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Factor cannot be negative');
        $money = new Money(200);
        $money->multiply(-2);
    }

    public function testCanMultiplyMoney(): void {
        $money = new Money(200);
        $result = $money->multiply(3);
        $this->assertEquals(600, $result->amount());
    }
}
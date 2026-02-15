<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CheckoutCartCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CheckoutCartCommandTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldCheckout_WhenCartExists(): void {
        $useCase = $this->createMock(CheckoutCartUseCase::class);
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('total')->willReturn(new Money(150));

        $useCase->expects($this->once())
            ->method('execute')
            ->willReturn($cartMock);

        $command = new CheckoutCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['cart_id' => '1', 'user_id' => '1']);

        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Carrinho finalizado! Total: 150', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenCartNotFound(): void {
        $useCase = $this->createMock(CheckoutCartUseCase::class);
        $useCase->method('execute')
            ->willThrowException(new \RuntimeException('Cart not found'));

        $command = new CheckoutCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['cart_id' => 'invalid-id', 'user_id' => '1']);

        $output = $commandTester->getDisplay();
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Cart not found', $output);
    }
}

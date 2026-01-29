<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\UseCase\CheckoutUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CheckoutCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CheckoutCommandTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldCheckout_WhenCartExists(): void {
        $useCase = $this->createMock(CheckoutUseCase::class);
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('total')->willReturn(new Money(150));

        $useCase->expects($this->once())
            ->method('execute')
            ->willReturn($cartMock);

        $command = new CheckoutCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['id' => '123']);

        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Carrinho finalizado! Total: 150', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenCartNotFound(): void {
        $useCase = $this->createMock(CheckoutUseCase::class);
        $useCase->method('execute')
            ->willThrowException(new \RuntimeException('Cart not found'));

        $command = new CheckoutCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['id' => 'invalid-id']);

        $output = $commandTester->getDisplay();
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Cart not found', $output);
    }
}

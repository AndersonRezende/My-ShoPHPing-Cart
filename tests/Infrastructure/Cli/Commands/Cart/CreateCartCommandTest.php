<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\UseCase\CreateCartUseCase;
use MyShoppingCart\Domain\Entity\Cart;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\CreateCartCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CreateCartCommandTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldCreateCart_WhenCalled(): void {
        $useCase = $this->createMock(CreateCartUseCase::class);
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('id')->willReturn('cart-uuid-123');

        $useCase->expects($this->once())
            ->method('execute')
            ->willReturn($cartMock);

        $command = new CreateCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Carrinho criado! ID: cart-uuid-123', $output);
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use DomainException;
use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\AssociateUserToCartCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AssociateUserToCartCommandTest extends TestCase {

    public function testExecuteShouldAssociateUserToCart(): void {
        $useCase = $this->createMock(AssociateUserToCartUseCase::class);
        $useCase->expects($this->once())->method('execute');
        $command = new AssociateUserToCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'cartId' => '1',
            'userId' => '1',
        ]);
        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('User associated to cart successfully.', $output);
    }

    public function testExecuteShouldNotAssociateUserToCartWhenAExceptionOccurs(): void {
        $useCase = $this->createMock(AssociateUserToCartUseCase::class);
        $useCase->expects($this->once())->method('execute')->willThrowException(new DomainException('User not found'));
        $command = new AssociateUserToCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'cartId' => '1',
            'userId' => '1',
        ]);
        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Error: User not found', $output);
    }
}

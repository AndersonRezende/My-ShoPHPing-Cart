<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands;

use MyShoppingCart\Application\UseCase\DeleteProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\DeleteProductCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DeleteProductCommandTest extends TestCase {

    public function testExecute_ShouldDeleteProduct_WhenProductExists(): void {
        $useCase = $this->createMock(DeleteProductUseCase::class);
        $useCase->expects($this->once())
            ->method('execute');

        $command = new DeleteProductCommand($useCase);
        $application = new Application();
        $application->addCommand($command);

        $commandTester = new CommandTester($application->find('msp:delete-product'));
        $commandTester->execute(['id' => '1']);

        $output = $commandTester->getDisplay();
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Produto deletado com sucesso! ID: 1', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenProductNotFound(): void {
        $useCase = $this->createMock(DeleteProductUseCase::class);
        $useCase->method('execute')->willThrowException(new \RuntimeException('Product not found'));

        $command = new DeleteProductCommand($useCase);
        $commandTester = new CommandTester($command);
        
        $commandTester->execute(['id' => '999']);
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }
}

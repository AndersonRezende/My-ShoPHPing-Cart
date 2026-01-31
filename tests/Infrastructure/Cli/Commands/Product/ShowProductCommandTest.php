<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\UseCase\ShowProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ShowProductCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ShowProductCommandTest extends TestCase {

    public function testExecute_ShouldShowProduct_WhenProductExists(): void {
        $useCase = $this->createMock(ShowProductUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willReturn(new Product('1', 'Arroz Parboilizado'));

        $command = new ShowProductCommand($useCase);
        $application = new Application();
        $application->addCommand($command);

        $commandTester = new CommandTester($application->find('msp:show-product'));
        $commandTester->execute(['id' => '1']);

        $output = $commandTester->getDisplay();
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Produto encontrado', $output);
        $this->assertStringContainsString('ID: 1', $output);
        $this->assertStringContainsString('Nome: Arroz Parboilizado', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenProductNotFound(): void {
        $useCase = $this->createMock(ShowProductUseCase::class);
        $useCase->method('execute')->willThrowException(new \RuntimeException('Product not found'));

        $command = new ShowProductCommand($useCase);
        $commandTester = new CommandTester($command);
        
        $commandTester->execute(['id' => '999']);
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }
}

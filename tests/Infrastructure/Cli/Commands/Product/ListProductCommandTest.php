<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\UseCase\Product\ListProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ListProductCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ListProductCommandTest extends TestCase {

    public function testExecuteListProductsCommand_ShouldListProducts_WhenThereAreProducts(): void {
        $listProductsUseCase = $this->createMock(ListProductUseCase::class);
        $listProductsUseCase->expects($this->once())
            ->method('execute')
            ->willReturn(array(
                new Product('1', 'Product 1'),
                new Product('2', 'Product 2')
            )
        );
        $listProductsCommand = new ListProductCommand($listProductsUseCase);
        $application = new Application();
        $application->addCommand($listProductsCommand);
        $command = $application->find('msp:list-products');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();
        $statusCode = $commandTester->getStatusCode();

        $this->assertEquals(Command::SUCCESS, $statusCode);
        $this->assertStringContainsString('ID', $output);
        $this->assertStringContainsString('Nome', $output);
        $this->assertStringContainsString('Product 1', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('Product 2', $output);
        $this->assertStringContainsString('2', $output);
    }

    public function testExecuteListProductsCommand_ShouldOnlyShowHeader_WhenThereIsNoProduct(): void {
        $listProductsUseCase = $this->createMock(ListProductUseCase::class);
        $listProductsUseCase->expects($this->once())
            ->method('execute')
            ->willReturn(array()
        );
        $listProductsCommand = new ListProductCommand($listProductsUseCase);
        $application = new Application();
        $application->addCommand($listProductsCommand);
        $command = $application->find('msp:list-products');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('ID', $output);
        $this->assertStringContainsString('Nome', $output);
    }
}
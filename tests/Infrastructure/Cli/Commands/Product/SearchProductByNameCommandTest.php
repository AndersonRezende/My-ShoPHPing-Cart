<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Product;

use Exception;
use MyShoppingCart\Application\UseCase\Product\ListProductUseCase;
use MyShoppingCart\Application\UseCase\Product\SearchProductByNameUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ListProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\SearchProductByNameCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class SearchProductByNameCommandTest extends TestCase {

    public function testExecuteListProductsCommand_ShouldListProducts_WhenThereAreProducts(): void {
        $searchProductByNameUseCase = $this->createMock(SearchProductByNameUseCase::class);
        $searchProductByNameUseCase->expects($this->once())
            ->method('execute')
            ->willReturn(array(
                    new Product('1', 'Macarrão Fettuccine'),
                    new Product('2', 'Macarrão Penne'),
                )
            );
        $searchProductByNameCommand = new SearchProductByNameCommand($searchProductByNameUseCase);
        $application = new Application();
        $application->addCommand($searchProductByNameCommand);
        $command = $application->find('msc:search-product-by-name');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'Macarrão'
        ]);

        $output = $commandTester->getDisplay();
        $statusCode = $commandTester->getStatusCode();

        $this->assertEquals(Command::SUCCESS, $statusCode);
        $this->assertStringContainsString('ID', $output);
        $this->assertStringContainsString('Nome', $output);
        $this->assertStringContainsString('Macarrão Fettuccine', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('Macarrão Penne', $output);
        $this->assertStringContainsString('2', $output);
    }

    public function testExecuteListProductsCommand_ShouldOnlyShowHeader_WhenThereIsNoProduct(): void {
        $searchProductByNameUseCase = $this->createMock(SearchProductByNameUseCase::class);
        $searchProductByNameUseCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new Exception('Product not found'));
        $listProductsCommand = new SearchProductByNameCommand($searchProductByNameUseCase);
        $application = new Application();
        $application->addCommand($listProductsCommand);
        $command = $application->find('msc:search-product-by-name');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'Macarrão'
        ]);

        $commandTester->getDisplay();

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }
}

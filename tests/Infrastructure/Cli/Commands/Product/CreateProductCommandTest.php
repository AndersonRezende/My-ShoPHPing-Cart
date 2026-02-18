<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use MyShoppingCart\Application\UseCase\Product\ListProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\CreateProductCommand;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\ListProductCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CreateProductCommandTest extends TestCase {

    public function testExecuteListProductsCommand_ShouldListProducts_WhenThereAreProducts(): void {
        $createProductUseCase = $this->createMock(CreateProductUseCase::class);
        $product = new Product('1', 'Product 1');
        $createProductUseCase->expects($this->once())
            ->method('execute')
            ->willReturn($product);
        $createProductCommand = new CreateProductCommand($createProductUseCase);
        $application = new Application();
        $application->addCommand($createProductCommand);
        $command = $application->find('msp:create-product');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['name' => $product->name()]);

        $output = $commandTester->getDisplay();
        $statusCode = $commandTester->getStatusCode();

        $this->assertEquals(Command::SUCCESS, $statusCode);
        $this->assertStringContainsString("Produto criado com sucesso! ID: {$product->id()} Nome: {$product->name()}", $output);
    }

    public function testExecute_ShouldAskForName_WhenNameIsNotProvided(): void {
        $useCase = $this->createMock(CreateProductUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->callback(function($input) {
                return $input->name === 'Novo Nome Interativo';
            }))
            ->willReturn(new Product('1', 'Novo Nome Interativo'));
        $command = new CreateProductCommand($useCase);
        $application = new Application();
        $application->addCommand($command);
        $commandTester = new CommandTester($application->find('msp:create-product'));
        $commandTester->setInputs(['Novo Nome Interativo']);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Nome do produto', $output);
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
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
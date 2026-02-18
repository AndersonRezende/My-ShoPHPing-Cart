<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Product\UpdateProductCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateProductCommandTest extends TestCase {

    public function testExecute_ShouldUpdateProduct_WhenProductExists(): void {
        $useCase = $this->createMock(UpdateProductUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willReturn(new Product('1', 'Arroz Parboilizado'));

        $command = new UpdateProductCommand($useCase);
        $application = new Application();
        $application->addCommand($command);
        
        $commandTester = new CommandTester($application->find('msp:update-product'));
        $commandTester->execute([
            'id' => '1',
            'name' => 'Arroz Parboilizado'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Produto atualizado com sucesso', $output);
    }

    public function testExecute_ShouldAskForName_WhenNameIsNotProvided(): void {
        $useCase = $this->createMock(UpdateProductUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->callback(function($input) {
                return $input->name === 'Novo Nome Interativo';
            }))
            ->willReturn(new Product('1', 'Novo Nome Interativo'));
        $command = new UpdateProductCommand($useCase);
        $application = new Application();
        $application->addCommand($command);
        $commandTester = new CommandTester($application->find('msp:update-product'));
        $commandTester->setInputs(['Novo Nome Interativo']);
        $commandTester->execute(['id' => '1',]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Novo nome do produto', $output);
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenProductNotFound(): void {
        $useCase = $this->createMock(UpdateProductUseCase::class);
        $useCase->method('execute')->willThrowException(new \RuntimeException('Product not found'));

        $command = new UpdateProductCommand($useCase);
        $commandTester = new CommandTester($command);
        
        $commandTester->execute(['id' => '999', 'name' => 'Test']);
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }
}

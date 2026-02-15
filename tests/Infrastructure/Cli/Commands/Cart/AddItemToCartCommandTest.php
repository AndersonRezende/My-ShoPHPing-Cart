<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\CartOutput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\AddItemToCartCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class AddItemToCartCommandTest extends TestCase {

    public function testExecute_ShouldAddItem_WhenInputIsValid(): void {
        $useCase = $this->createMock(AddItemToCartUseCase::class);
        $useCase->expects($this->once())->method('execute')->willReturn(new CartOutput(1, [new Product('1', 'Arroz')]));

        $command = new AddItemToCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'cart_id' => '1',
            'user_id' => '1',
            'product_id' => '1',
            'quantity' => '2',
            'unit_price' => '50',
            'description' => 'Item Teste',
        ]);

        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Item adicionado ao carrinho com sucesso!', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecute_ShouldFail_WhenUseCaseThrowsException(): void {
        $useCase = $this->createMock(AddItemToCartUseCase::class);
        $useCase->method('execute')
            ->willThrowException(new \RuntimeException('Product not found'));

        $command = new AddItemToCartCommand($useCase);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'cart_id' => 'cart-1',
            'user_id' => 'cart-1',
            'product_id' => 'prod-999',
            'quantity' => '1',
            'unit_price' => '10'
        ]);

        $output = $commandTester->getDisplay();
        
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Product not found', $output);
    }
}

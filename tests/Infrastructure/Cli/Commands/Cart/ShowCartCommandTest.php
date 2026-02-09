<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\Cart;

use InvalidArgumentException;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use MyShoppingCart\Domain\Entity\Cart\CartBuilder;
use MyShoppingCart\Domain\Entity\CartItem;
use MyShoppingCart\Domain\Entity\Product;
use MyShoppingCart\Domain\Enum\CartStatus;
use MyShoppingCart\Domain\ValueObject\Money;
use MyShoppingCart\Infrastructure\Cli\Commands\Cart\ShowCartCommand;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ShowCartCommandTest extends TestCase {

#[AllowMockObjectsWithoutExpectations]
    public function testExecuteShouldFailWhenCartDoesNotExist(): void {
        $useCase = $this->createMock(ShowCartUseCase::class);
        $useCase->method('execute')
            ->willThrowException(new InvalidArgumentException('Cart not found'));
        $command = new ShowCartCommand($useCase);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(['id' => 'non-existent-cart-id',]);
        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('Cart not found', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteShouldSucceedWhenCartExists(): void {
        $cartItem = new CartItem(
            null,
            new Product('prod-1', 'Product 1'),
            2,
            new Money(1500)
        );
        $useCase = $this->createMock(ShowCartUseCase::class);
        $useCase->method('execute')->willReturn(new CartBuilder()
            ->withId('cart-1')
            ->withCartItems([$cartItem])
            ->build());

        $command = new ShowCartCommand($useCase);
        $commandTester = new CommandTester($command);
        $commandTester->execute(['id' => 'cart-1',]);

        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('cart-1', $output);
        $this->assertStringContainsString(CartStatus::OPENED->value, $output);
        $this->assertStringContainsString('Número de Itens', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('ID do Produto', $output);
        $this->assertStringContainsString('prod-1', $output);
        $this->assertStringContainsString('Nome', $output);
        $this->assertStringContainsString('Product 1', $output);
        $this->assertStringContainsString('Quantidade', $output);
        $this->assertStringContainsString('2', $output);
        $this->assertStringContainsString('Preço Unitário', $output);
        $this->assertStringContainsString('1500', $output);
        $this->assertStringContainsString('Subtotal', $output);
        $this->assertStringContainsString('3000', $output);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testExecuteShouldSucceedWhenCartNotContainsItemsAndExists(): void {
        $useCase = $this->createMock(ShowCartUseCase::class);
        $useCase->method('execute')->willReturn(new CartBuilder()
            ->withId('cart-1')
            ->build());

        $command = new ShowCartCommand($useCase);
        $commandTester = new CommandTester($command);
        $commandTester->execute(['id' => 'cart-1',]);

        $output = $commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('O carrinho está vazio.', $output);
    }
}

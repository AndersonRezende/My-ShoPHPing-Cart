<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\CreateCartInput;
use MyShoppingCart\Application\UseCase\Cart\CreateCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCartCommand extends Command {
    public function __construct(private readonly CreateCartUseCase $createCartUseCase) {
        return parent::__construct('msp:create-cart');
    }

    protected function configure(): void {
        $this->setDescription('Inicia um novo carrinho de compras.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        
        $cart = $this->createCartUseCase->execute(new CreateCartInput());

        $io->success("Carrinho criado! ID: {$cart->id()}");

        return Command::SUCCESS;
    }
}
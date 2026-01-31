<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckoutCommand extends Command {
    public function __construct(private readonly CheckoutCartUseCase $checkoutUseCase) {
        parent::__construct('msp:checkout');
    }

    protected function configure(): void {
        $this->setDescription('Finaliza um carrinho de compras.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID do carrinho');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        try {
            $cart = $this->checkoutUseCase->execute(new CheckoutInput($id));
            $io->success("Carrinho finalizado! Total: {$cart->total()->amount()}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

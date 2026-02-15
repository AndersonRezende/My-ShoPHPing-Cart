<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\CheckoutInput;
use MyShoppingCart\Application\UseCase\Cart\CheckoutCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckoutCartCommand extends Command {
    public function __construct(private readonly CheckoutCartUseCase $checkoutUseCase) {
        parent::__construct('msp:checkout');
    }

    protected function configure(): void {
        $this->setDescription('Finaliza um carrinho de compras.')
            ->addArgument('cart_id', InputArgument::REQUIRED, 'ID do carrinho')
            ->addArgument('user_id', InputArgument::REQUIRED, 'ID do usuário');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $cartId = $input->getArgument('cart_id');
        $userId = $input->getArgument('user_id');

        try {
            $cart = $this->checkoutUseCase->execute(new CheckoutInput($cartId, $userId));
            $io->success("Carrinho finalizado! Total: {$cart->total()->amount()}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

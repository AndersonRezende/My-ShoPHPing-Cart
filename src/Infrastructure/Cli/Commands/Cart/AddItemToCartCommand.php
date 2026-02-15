<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\AddItemToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AddItemToCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddItemToCartCommand extends Command {
    public function __construct(private readonly AddItemToCartUseCase $addItemToCart) {
        parent::__construct('msp:add-item-to-cart');
    }

    protected function configure(): void {
        $this->setDescription('Adiciona um item ao carrinho de compras.')
            ->addArgument('cart_id', InputArgument::REQUIRED, 'ID do carrinho')
            ->addArgument('user_id', InputArgument::REQUIRED, 'ID do usuário')
            ->addArgument('product_id', InputArgument::REQUIRED, 'ID do produto')
            ->addArgument('quantity', InputArgument::REQUIRED, 'Quantidade do produto')
            ->addArgument('unit_price', InputArgument::OPTIONAL, 'Preço unitário do produto', 0)
            ->addArgument('description', InputArgument::OPTIONAL, 'Descrição do produto', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $cartId = $input->getArgument('cart_id');
        $userId = $input->getArgument('user_id');
        $productId = $input->getArgument('product_id');
        $description = $input->getArgument('description') ?? '';
        $quantity = (int) $input->getArgument('quantity');
        $unitPrice = (int) $input->getArgument('unit_price');

        try {
            $this->addItemToCart->execute(new AddItemToCartInput(
                $cartId,
                $userId,
                $productId,
                $description,
                $quantity,
                $unitPrice
            ));
            $io->success("Item adicionado ao carrinho com sucesso!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
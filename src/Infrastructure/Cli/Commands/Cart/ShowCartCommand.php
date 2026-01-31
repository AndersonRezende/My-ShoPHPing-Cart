<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\ShowCartInput;
use MyShoppingCart\Application\UseCase\Cart\ShowCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowCartCommand extends Command {
    public function __construct(private readonly ShowCartUseCase $showCartUseCase) {
        return parent::__construct('msp:show-cart');
    }

    protected function configure(): void {
        $this->setDescription('Mostra os detalhes de um carrinho de compras.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID do carrinho');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Detalhes do Carrinho de Compras');
        
        $id = $input->getArgument('id');
        try {
            $cart = $this->showCartUseCase->execute(new ShowCartInput($id));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        
        $rows = [
            [$cart->id(), $cart->status()->value, count($cart->items())],
        ];
        $io->table(['ID', 'Status', 'Número de Itens'], $rows);

        $io->newLine();
        $io->section('Itens no Carrinho:');
        if (empty($cart->items())) {
            $io->writeln('O carrinho está vazio.');
        } else {
            $itemRows = array_map(fn($item) => [
                $item->product()->id(),
                $item->product()->name(),
                $item->quantity(),
                $item->unitPrice()->amount(),
                $item->subTotal()->amount()
            ], $cart->items());
            $io->table(['ID do Produto', 'Nome', 'Quantidade', 'Preço Unitário', 'Subtotal'], $itemRows);
        }

        return Command::SUCCESS;
    }
}
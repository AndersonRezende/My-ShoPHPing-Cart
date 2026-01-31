<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\UseCase\Product\ListProductsUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListProductsCommand extends Command {

    public function __construct(private readonly ListProductsUseCase $listProductsUseCase) {
        parent::__construct('msp:list-products');
    }

    protected function configure(): void {
        $this->setDescription('Lista todos os produtos disponíveis.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Produtos Disponíveis');

        $products = $this->listProductsUseCase->execute();

        $rows = array_map(fn($product) => [
            $product->id(),
            $product->name(),
        ], $products);

        $io->table(['ID', 'Nome'], $rows);

        return Command::SUCCESS;
    }
}
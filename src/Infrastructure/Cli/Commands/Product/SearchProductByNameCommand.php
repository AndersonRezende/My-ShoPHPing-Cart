<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\DTO\SearchProductByNameInput;
use MyShoppingCart\Application\UseCase\Product\SearchProductByNameUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchProductByNameCommand extends Command {
    public function __construct(private readonly SearchProductByNameUseCase $searchProductByNameUseCase) {
        parent::__construct('msc:search-product-by-name');
    }

    protected function configure(): void {
        $this->setDescription('Mostra produtos existentes com determinado nome.')
            ->addArgument('name', InputArgument::REQUIRED, 'Nome do produto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mostra produtos existentes com determinado nome.');

        $name = $input->getArgument('name');

        try {
            $products = $this->searchProductByNameUseCase->execute(new SearchProductByNameInput($name));

            $rows = array_map(fn($product) => [
                $product->id(),
                $product->name(),
            ], $products);

            $io->table(['ID', 'Nome'], $rows);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

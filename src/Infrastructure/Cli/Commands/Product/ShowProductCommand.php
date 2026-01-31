<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\DTO\ShowProductInput;
use MyShoppingCart\Application\UseCase\ShowProductUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowProductCommand extends Command {
    public function __construct(private ShowProductUseCase $showProductUseCase) {
        parent::__construct('msp:show-product');
    }

    protected function configure(): void {
        $this->setDescription('Mostra um produto existente.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID do produto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mostra um produto existente.');

        $id = $input->getArgument('id');

        try {
            $product = $this->showProductUseCase->execute(new ShowProductInput($id));
            $io->success("Produto encontrado! ID: {$product->id()} Nome: {$product->name()}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

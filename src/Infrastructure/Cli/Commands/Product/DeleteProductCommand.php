<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\DTO\DeleteProductInput;
use MyShoppingCart\Application\UseCase\DeleteProductUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteProductCommand extends Command {
    public function __construct(private DeleteProductUseCase $deleteProductUseCase) {
        parent::__construct('msp:delete-product');
    }

    protected function configure(): void {
        $this->setDescription('Deleta um produto existente.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID do produto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Deleta um produto existente.');

        $id = $input->getArgument('id');

        try {
            $this->deleteProductUseCase->execute(new DeleteProductInput($id));
            $io->success("Produto deletado com sucesso! ID: $id");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

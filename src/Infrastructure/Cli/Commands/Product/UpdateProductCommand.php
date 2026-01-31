<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\DTO\UpdateProductInput;
use MyShoppingCart\Application\UseCase\Product\UpdateProductUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateProductCommand extends Command {
    public function __construct(private UpdateProductUseCase $updateProductUseCase) {
        parent::__construct('msp:update-product');
    }

    protected function configure(): void {
        $this->setDescription('Atualiza um produto existente.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID do produto')
            ->addArgument('name', InputArgument::OPTIONAL, 'Novo nome do produto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Atualização de Produto');

        $id = $input->getArgument('id');
        $name = $input->getArgument('name');

        if (!$name) {
            $name = $io->ask('Novo nome do produto');
        }

        try {
            $product = $this->updateProductUseCase->execute(new UpdateProductInput($id, $name));
            $io->success("Produto atualizado com sucesso! ID: {$product->id()} Nome: {$product->name()}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

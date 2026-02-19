<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Product;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\UseCase\Product\CreateProductUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateProductCommand extends Command {
    public function __construct(private readonly CreateProductUseCase $createProductUseCase) {
        return parent::__construct('msp:create-product');
    }

    protected function configure(): void {
        $this->setDescription('Cria um novo produto.')
            ->addArgument('name', InputArgument::REQUIRED, 'Nome do produto')
            ->addArgument('category_id', InputArgument::OPTIONAL, 'Id da categoria do produto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Cadastro de Produtos');

        $name = $input->getArgument('name');
        $categoryId = $input->getArgument('category_id') ?? null;

        $product = $this->createProductUseCase->execute(new CreateProductInput($name, $categoryId));

        $io->success("Produto criado com sucesso! ID: {$product->id()} Nome: {$product->name()}");

        return Command::SUCCESS;
    }
}
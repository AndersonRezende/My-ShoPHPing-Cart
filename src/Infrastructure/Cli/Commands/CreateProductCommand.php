<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands;

use MyShoppingCart\Application\DTO\CreateProductInput;
use MyShoppingCart\Application\UseCase\CreateProductUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateProductCommand extends Command {
    public function __construct(private CreateProductUseCase $createProductUseCase) {
        return parent::__construct('msp:create-product');
    }

    protected function configure(): void {
        $this->setDescription('Cria um novo produto.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Cadastro de Produtos');

        $io->section('Criar um novo produto');
        $name = $io->ask('Nome do produto');

        $product = $this->createProductUseCase->execute(new CreateProductInput($name));

        $io->success("Produto criado com sucesso! ID: {$product->id()} Nome: {$product->name()}");

        return Command::SUCCESS;
    }
}
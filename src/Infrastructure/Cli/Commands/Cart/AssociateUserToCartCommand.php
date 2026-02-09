<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssociateUserToCartCommand extends Command {

    public function __construct(private readonly AssociateUserToCartUseCase $associateUserToCartUseCase) {
        parent::__construct('cart:associate-user');
    }

    protected function configure(): void {
        $this->setDescription('Associate a user to a cart')
            ->addArgument('cartId', InputArgument::REQUIRED, 'Cart ID')
            ->addArgument('userId', InputArgument::REQUIRED, 'User ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            $this->associateUserToCartUseCase->execute(
                $input->getArgument('cartId'),
                $input->getArgument('userId')
            );
            $output->writeln('User associated to cart successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

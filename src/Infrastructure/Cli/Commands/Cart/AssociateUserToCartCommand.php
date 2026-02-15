<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\Cart;

use MyShoppingCart\Application\DTO\AssociateUserToCartInput;
use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssociateUserToCartCommand extends Command {

    public function __construct(private readonly AssociateUserToCartUseCase $associateUserToCartUseCase) {
        parent::__construct('cart:associate-user');
    }

    protected function configure(): void {
        $this->setDescription('Associate a user to a cart')
            ->addArgument('cart_id', InputArgument::REQUIRED, 'Cart ID')
            ->addArgument('owner_user_id', InputArgument::REQUIRED, 'Owner User ID')
            ->addArgument('user_id', InputArgument::REQUIRED, 'User ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $cartId = $input->getArgument('cart_id');
        $ownerUserId = $input->getArgument('owner_user_id');
        $userId = $input->getArgument('user_id');

        try {
            $input = new AssociateUserToCartInput($cartId, $ownerUserId, $userId);
            $this->associateUserToCartUseCase->execute($input);
            $output->writeln('User associated to cart successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

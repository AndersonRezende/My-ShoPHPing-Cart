<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\User;

use MyShoppingCart\Application\UseCase\User\LoginUserUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoginUserCommand extends Command {

    public function __construct(private readonly LoginUserUseCase $loginUserUseCase) {
        parent::__construct('msp:login-user');
    }

    protected function configure(): void {
        $this->setDescription('Login a user')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            $user = $this->loginUserUseCase->execute(
                $input->getArgument('email'),
                $input->getArgument('password')
            );
            $output->writeln("Login successful. User ID: " . $user->id());
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Cli\Commands\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterUserCommand extends Command {

    public function __construct(private readonly RegisterUserUseCase $registerUserUseCase) {
        parent::__construct('msp:register-user');
    }

    protected function configure(): void {
        $this->setDescription('Register a new user')
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        try {
            $registerUserInput = new RegisterUserInput(
                $input->getArgument('name'),
                $input->getArgument('email'),
                $input->getArgument('password')
            );

            $user = $this->registerUserUseCase->execute($registerUserInput);
            $output->writeln("User registered successfully with ID: " . $user->id());
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

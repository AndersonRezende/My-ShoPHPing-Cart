<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Cli\Commands\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Cli\Commands\User\RegisterUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RegisterUserCommandTest extends TestCase {

    public function testExecuteShouldRegisterUserSuccessfully(): void {
        $useCase = $this->createMock(RegisterUserUseCase::class);
        $user = new User(
            'user-123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );

        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(RegisterUserInput::class))
            ->willReturn($user);

        $command = new RegisterUserCommand($useCase);
        $application = new Application();
        $application->addCommand($command);

        $command = $application->find('msp:register-user');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'name' => 'Anderson',
            'email' => 'anderson@example.com',
            'password' => 'password123'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('User registered successfully with ID: user-123', $output);
    }

    public function testExecuteShouldFailWhenUseCaseThrowsException(): void {
        $useCase = $this->createMock(RegisterUserUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new \DomainException('User already exists'));

        $command = new RegisterUserCommand($useCase);
        $application = new Application();
        $application->addCommand($command);
        
        $command = $application->find('msp:register-user');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'name' => 'Anderson',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('Error: User already exists', $output);
    }
}

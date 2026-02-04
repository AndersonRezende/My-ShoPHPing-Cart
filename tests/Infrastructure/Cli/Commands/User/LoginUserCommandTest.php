<?php declare(strict_types=1);

namespace Infrastructure\Cli\Commands\User;

use MyShoppingCart\Application\DTO\LoginUserInput;
use MyShoppingCart\Application\UseCase\User\LoginUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Cli\Commands\User\LoginUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class LoginUserCommandTest extends TestCase {

    public function testExecuteShouldLoginUserSuccessfully(): void {
        $useCase = $this->createMock(LoginUserUseCase::class);
        $user = new User(
            'user-123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );

        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(LoginUserInput::class))
            ->willReturn($user);

        $command = new LoginUserCommand($useCase);
        $application = new Application();
        $application->addCommand($command);

        $command = $application->find('msp:login-user');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'email' => 'anderson@example.com',
            'password' => 'password123'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Login successful. User ID: user-123', $output);
    }

    public function testExecuteShouldFailWhenUseCaseThrowsException(): void {
        $useCase = $this->createMock(LoginUserUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new \DomainException('Invalid email or password'));

        $command = new LoginUserCommand($useCase);
        $application = new Application();
        $application->addCommand($command);
        
        $command = $application->find('msp:login-user');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('Error: Invalid email or password', $output);
    }
}

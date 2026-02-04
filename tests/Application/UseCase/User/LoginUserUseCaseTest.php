<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\User;

use MyShoppingCart\Application\DTO\LoginUserInput;
use MyShoppingCart\Application\UseCase\User\LoginUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class LoginUserUseCaseTest extends TestCase {
    public function testLoginUser(): void {
        $user = new User(
            '123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $loginUserInput = new LoginUserInput('anderson@example.com', 'password123');
        $useCase = new LoginUserUseCase($userRepository);
        $loggedInUser = $useCase->execute($loginUserInput);

        $this->assertSame($user, $loggedInUser);
    }

    public function testLoginUserWithInvalidCredentials(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Invalid email or password");

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $loginUserInput = new LoginUserInput('anderson@example.com', 'password123');
        $useCase = new LoginUserUseCase($userRepository);
        $useCase->execute($loginUserInput);
    }
}

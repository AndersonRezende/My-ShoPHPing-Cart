<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class RegisterUserUseCaseTest extends TestCase {
    public function testRegisterUser(): void {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);
        $userRepository->expects($this->once())
            ->method('save');

        $useCase = new RegisterUserUseCase($userRepository);
        $input = new RegisterUserInput('Anderson', 'anderson@example.com', 'password123');
        $user = $useCase->execute($input);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Anderson', $user->name());
        $this->assertEquals('anderson@example.com', $user->email()->value());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRegisterUserWithExistingEmail(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("User with this email already exists");

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn($this->createMock(User::class));

        $useCase = new RegisterUserUseCase($userRepository);
        $input = new RegisterUserInput('Anderson', 'anderson@example.com', 'password123');
        $useCase->execute($input);
    }
}

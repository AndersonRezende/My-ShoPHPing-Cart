<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Application\UseCase\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
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
        $uuidGenerator = $this->createMock(IdGeneratorInterface::class);
        $uuidGenerator->expects($this->once())->method('generate')->willReturn('1');

        $useCase = new RegisterUserUseCase($userRepository, $uuidGenerator);
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
        $uuidGenerator = $this->createMock(IdGeneratorInterface::class);

        $useCase = new RegisterUserUseCase($userRepository, $uuidGenerator);
        $input = new RegisterUserInput('Anderson', 'anderson@example.com', 'password123');
        $useCase->execute($input);
    }
}

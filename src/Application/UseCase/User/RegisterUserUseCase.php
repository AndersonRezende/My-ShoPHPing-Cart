<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;

readonly class RegisterUserUseCase {
    public function __construct(private UserRepository $userRepository) {}

    public function execute(RegisterUserInput $input): User {
        $emailVo = new Email($input->email);
        
        if ($this->userRepository->findByEmail($emailVo)) {
            throw new \DomainException("User with this email already exists");
        }

        $user = new User(
            uniqid(),
            $input->name,
            $emailVo,
            Password::hash($input->password)
        );

        $this->userRepository->save($user);

        return $user;
    }
}

<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\Service\IdGeneratorInterface;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;

readonly class RegisterUserUseCase {
    public function __construct(
        private UserRepository $userRepository,
        private IdGeneratorInterface $idGenerator
    ) {}

    public function execute(RegisterUserInput $input): User {
        $emailVo = new Email($input->email);
        
        if ($this->userRepository->findByEmail($emailVo)) {
            throw new \DomainException("User with this email already exists");
        }

        $id = $this->idGenerator->generate();
        $user = new User(
            $id,
            $input->name,
            $emailVo,
            Password::hash($input->password)
        );

        $this->userRepository->save($user);

        return $user;
    }
}

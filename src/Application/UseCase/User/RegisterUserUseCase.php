<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\User;

use Exception;
use InvalidArgumentException;
use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Exception\ResourceNotFoundException;
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

        if ($this->isEmailInUse($emailVo)) {
            throw new InvalidArgumentException("This Email $input->email is already in use.");
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

    private function isEmailInUse(Email $email): bool {
        try {
            $this->userRepository->findByEmail($email);
        } catch (ResourceNotFoundException $exception) {
            return false;
        }
        return true;
    }
}

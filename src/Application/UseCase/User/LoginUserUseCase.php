<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\User;

use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\ValueObject\Email;

class LoginUserUseCase {
    public function __construct(private UserRepository $userRepository) {}

    public function execute(string $email, string $password): User {
        $emailVo = new Email($email);
        $user = $this->userRepository->findByEmail($emailVo);

        if (!$user || !$user->password()->verify($password)) {
            throw new \DomainException("Invalid email or password");
        }

        return $user;
    }
}

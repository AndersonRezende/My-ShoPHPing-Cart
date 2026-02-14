<?php declare(strict_types=1);

namespace MyShoppingCart\Application\UseCase\User;

use DomainException;
use InvalidArgumentException;
use MyShoppingCart\Application\DTO\LoginUserInput;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\ValueObject\Email;

readonly class LoginUserUseCase {
    public function __construct(private UserRepository $userRepository) {}

    /** @throws DomainException|InvalidArgumentException */
    public function execute(LoginUserInput $loginUserInput): User {
        $emailVo = new Email($loginUserInput->email);
        $user = $this->userRepository->findByEmail($emailVo);

        if (!$user || !$user->password()->verify($loginUserInput->password)) {
            throw new DomainException('Invalid email or password');
        }

        return $user;
    }
}

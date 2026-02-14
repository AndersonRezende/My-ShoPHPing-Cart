<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Persistence\Pdo;

use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Repository\UserRepository;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use PDO;

readonly class UserRepositoryPdo implements UserRepository {
    public function __construct(private PDO $pdo) {}

    public function save(User $user): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, name, email, password) 
            VALUES (:id, :name, :email, :password)
            ON CONFLICT(id) DO UPDATE SET name = :name, password = :password
        ");
        
        $result = $stmt->execute([
            'id' => $user->id(),
            'name' => $user->name(),
            'email' => $user->email()->value(),
            'password' => $user->password()->value()
        ]);

        if (!$result) {
            throw new \RuntimeException('Failed to save user.');
        }
    }

    public function findById(string $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateUser($data);
    }

    public function findByEmail(Email $email): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email->value()]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateUser($data);
    }

    private function hydrateUser(array $data): User {
        return new User(
            $data['id'],
            $data['name'],
            new Email($data['email']),
            new Password($data['password'])
        );
    }
}

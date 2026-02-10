<?php declare(strict_types=1);

namespace Infrastructure\Persistence\Pdo;

use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Persistence\Pdo\UserRepositoryPdo;
use MyShoppingCart\Tests\Infrastructure\Persistence\Pdo\DatabaseTestCase;
use PDO;
use PDOStatement;
use RuntimeException;

class UserRepositoryPdoTest extends DatabaseTestCase {
    
    public function testShouldThrowRuntimeExceptionWhenAnErrorOccurs(): void {
        $this->expectException(RuntimeException::class);

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($pdoStatementMock);
        $repository = new UserRepositoryPdo($pdoMock);

        $repository->save(
            new User(null, 'teste', new Email('teste@email.com'), new Password('123456'))
        );
    }

    public function testShouldCreateNewUser(): void {
        $repository = new UserRepositoryPdo($this->connection);
        $user = new User(
            null,
            $name = 'Testevaldo',
            $email = new Email('testevaldo@email.com'),
            $password = Password::hash('testevaldosenha'));
        $repository->save($user);

        $stmt = $this->connection->query('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email->value()]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($result['id']);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email->value(), $result['email']);
        $this->assertEquals($password->value(), $result['password']);
    }

    public function testShouldFindById(): void {
        $this->connection->exec("INSERT INTO users (id, name, email, password) VALUES (1, 'Testevaldo', 'testevaldo@email.com', '123456')");
        $repository = new UserRepositoryPdo($this->connection);
        $user = $repository->findById('1');

        $this->assertNotNull($user);
        $this->assertEquals('1', $user->id());
        $this->assertEquals('Testevaldo', $user->name());
        $this->assertEquals('testevaldo@email.com', $user->email()->value());
        $this->assertEquals('123456', $user->password()->value());
    }

    public function testShouldFindByEmail(): void {
        $this->connection->exec("INSERT INTO users (id, name, email, password) VALUES (1, 'Testevaldo', 'testevaldo@email.com', '123456')");
        $repository = new UserRepositoryPdo($this->connection);
        $user = $repository->findByEmail(new Email('testevaldo@email.com'));

        $this->assertNotNull($user);
        $this->assertEquals('1', $user->id());
        $this->assertEquals('Testevaldo', $user->name());
        $this->assertEquals('testevaldo@email.com', $user->email()->value());
        $this->assertEquals('123456', $user->password()->value());
    }

    public function testShouldReturnNullWhenUserDoesNotExistAndFindById(): void {
        $repository = new UserRepositoryPdo($this->connection);

        $user = $repository->findById('1');

        $this->assertNull($user);
    }

    public function testShouldReturnNullWhenUserDoesNotExistAndFindByEmail(): void {
        $repository = new UserRepositoryPdo($this->connection);

        $user = $repository->findByEmail(new Email('teste@email.com'));

        $this->assertNull($user);
    }
}
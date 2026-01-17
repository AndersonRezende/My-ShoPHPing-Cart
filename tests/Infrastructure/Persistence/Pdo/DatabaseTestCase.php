<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Persistence\Pdo;

use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase {

    protected \PDO $connection;

    protected function tearDown(): void {
        $this->connection->rollBack();
        parent::tearDown();
    }

    protected function setUp(): void {
        parent::setUp();
        $this->connection = SqliteTestHelper::createConnection();
        $this->connection->beginTransaction();
    }
}
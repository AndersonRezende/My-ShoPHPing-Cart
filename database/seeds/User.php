<?php

declare(strict_types=1);

use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Service\UuidGenerator;
use Phinx\Seed\AbstractSeed;

class User extends AbstractSeed
{
    /**
     * Run Method using "php vendor/bin/phinx seed:run -s Users".
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'id' => $this->generateId(),
                'name' => 'Anderson',
                'email' => 'andersonrezende17@hotmail.com',
                'password' => Password::hash('123456')->value()
            ],
        ];

        $users = $this->table('users');
        $this->execute('DELETE FROM users');
        $users->insert($data)->save();
    }

    private function generateId(): string {
        return new UuidGenerator()->generate();
    }
}

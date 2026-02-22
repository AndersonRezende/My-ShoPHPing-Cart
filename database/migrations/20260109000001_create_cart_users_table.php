<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('cart_users', ['id' => false, 'primary_key' => ['cart_id', 'user_id']])
            ->addColumn('cart_id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('user_id', 'string', ['limit' => 36, 'null' => false])
            ->addForeignKey('cart_id', 'carts', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->create();
    }
}

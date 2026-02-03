<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('cart_users', ['id' => false, 'primary_key' => ['cart_id', 'user_id']]);
        $table->addColumn('cart_id', 'string', ['limit' => 36])
              ->addColumn('user_id', 'string', ['limit' => 36])
              ->addForeignKey('cart_id', 'carts', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
              ->create();
    }
}

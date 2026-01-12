<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartItemsTable extends AbstractMigration {
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void {
        $this->table('cart_items')
            ->addColumn('cart_id', 'integer')
            ->addColumn('product_id', 'integer', ['null' => true])
            ->addColumn('quantity', 'integer')
            ->addColumn('unit_price', 'integer')
            ->addForeignKey('cart_id', 'carts', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('product_id', 'products', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();

    }
}

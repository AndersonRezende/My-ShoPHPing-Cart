<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartItemsTable extends AbstractMigration {
    public function change(): void {
        $this->table('cart_items')
            ->addColumn('cart_id', 'string', ['limit' => 36])
            ->addColumn('product_id', 'integer', ['null' => true])
            ->addColumn('quantity', 'integer')
            ->addColumn('unit_price', 'integer')
            ->addTimestamps()
            ->addForeignKey('cart_id', 'carts', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('product_id', 'products', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addIndex(['cart_id', 'product_id'], ['unique' => true])
            ->create();
    }
}

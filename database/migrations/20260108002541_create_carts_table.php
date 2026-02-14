<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCartsTable extends AbstractMigration {
    public function change(): void {
        $this->table('carts', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 20])
            ->addTimestamps()
            ->create();
    }
}

<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriesTable extends AbstractMigration {
    public function change(): void {
        $this->table('categories', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addTimestamps()
            ->create();
    }
}

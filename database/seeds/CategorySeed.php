<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Service\UuidGenerator;
use Phinx\Seed\AbstractSeed;

class CategorySeed extends AbstractSeed
{
    public function run(): void
    {
        $categories = [
            'Mercearia Seca (Grãos e Massas)',
            'Matinais e Sobremesas',
            'Temperos, Óleos e Molhos',
            'Hortifrúti',
            'Gelados e Congelados',
            'Açogue e Embutidos',
            'Conservas e Enlatados',
            'Bebidas e Snacks',
            'Limpeza',
            'Higiene e Cuidados Pessoais'
        ];

        $table = $this->table('categories');

        if ($this->hasTable('categories')) {
            $count = $this->fetchRow('SELECT COUNT(*) as count FROM categories');
            if ($count['count'] > 0) {
                return;
            }
        }

        $data = [];
        $generator = new UuidGenerator();
        
        foreach ($categories as $name) {
            $data[] = [
                'id' => $generator->generate(),
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $table->insert($data)->save();
    }
}

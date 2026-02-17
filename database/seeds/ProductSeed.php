<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Service\UuidGenerator;
use Phinx\Seed\AbstractSeed;

class ProductSeed extends AbstractSeed
{
    public function run(): void
    {
        $productsMap = [
            'Mercearia Seca (Grãos e Massas)' => [
                'Arroz', 'Feijão Marrom', 'Feijão Preto', 'Macarrão Linguine',
                'Macarrão Penne', 'Macarrão Parafuso', 'Macarrão Rigattoni',
                'Cuscuz', 'Tapioca', 'Farinha de Trigo', 'Farinha', 'Maizena', 'Rap10'
            ],
            'Matinais e Sobremesas' => [
                'Café em Pó', 'Leite em Pó', 'Achocolatado em Pó', 'Nesquik',
                'Açucar', 'Creme de Leite', 'Leite Condensado'
            ],
            'Temperos, Óleos e Molhos' => [
                'Óleo', 'Sal', 'Vinagre', 'Maionese Vigor', 'Maionese Heinz',
                'Ketchup', 'Mostarda', 'Shoyo', 'Barbecue', 'Molho de Alho',
                'Corante', 'Oregano', 'Louro', 'Pimenta', 'Sazon'
            ],
            'Hortifrúti' => [
                'Batata', 'Cenoura', 'Cebola', 'Alho', 'Pimentão', 'Tomate',
                'Repolho', 'Alface', 'Brócolis', 'Coentro', 'Limão', 'Laranja', 'Melão'
            ],
            'Gelados e Congelados' => [
                'Margarina', 'Queijo', 'Danone', 'Requeijão', 'Leite de Caixinha',
                'Achocolatado Líquido', 'Pizza', 'Nugget', 'Polpa de Frutas',
                'Ovo', 'Ovo de Codorna'
            ],
            'Açogue e Embutidos' => [
                'Carne', 'Peito de Frango', 'Calabresa', 'Charque', 'Frango',
                'Salsicha', 'Salame', 'Presunto', 'Mortadela', 'Peito de Peru'
            ],
            'Conservas e Enlatados' => [
                'Milho', 'Azeitona', 'Queijo Ralado'
            ],
            'Bebidas e Snacks' => [
                'Água com Gás', 'Suco de Pacote', 'Salgadinho'
            ],
            'Limpeza' => [
                'Sabão em Pó', 'Sabão em Barra', 'Sabão Liquido', 'Amaciante',
                'Detergente', 'Esponja', 'Bombril', 'Veja', 'Cif', 'Água Sanitária',
                'Desinfetante', 'Pato', 'Veneno', 'Óleo de Peroba', 'Papel Toalha'
            ],
            'Higiene e Cuidados Pessoais' => [
                'Sabonete', 'Sabonete Liquido', 'Shampoo', 'Condicionador',
                'Pasta de Dente', 'Enxaguante Bucal', 'Desodorante', 'Acetona',
                'Papel Higiênico'
            ]
        ];

        $categories = $this->fetchAll('SELECT id, name FROM categories');
        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category['name']] = $category['id'];
        }

        $data = [];
        $generator = new UuidGenerator();
        $now = date('Y-m-d H:i:s');

        foreach ($productsMap as $categoryName => $productNames) {
            $categoryId = $categoryMap[$categoryName] ?? null;
            
            foreach ($productNames as $productName) {
                $data[] = [
                    'id' => $generator->generate(),
                    'name' => $productName,
                    'category_id' => $categoryId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        $table = $this->table('products');
        if ($this->hasTable('products')) {
             $table->truncate();
        }
        $table->insert($data)->save();
    }
}

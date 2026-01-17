<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Products extends AbstractSeed
{
    /**
     * Run Method using "php vendor/bin/phinx seed:run -s Products".
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Arroz',],
            ['name' => 'Feijão Marrom',],
            ['name' => 'Feijão Preto',],
            ['name' => 'Macarrão Linguine',],
            ['name' => 'Macarrão Penne',],
            ['name' => 'Macarrão Parafuso',],
            ['name' => 'Macarrão Rigattoni',],
            ['name' => 'Cuscuz',],
            ['name' => 'Tapioca',],
            ['name' => 'Farinha de Trigo',],
            ['name' => 'Farinha',],
            ['name' => 'Café em Pó',],
            ['name' => 'Leite em Pó',],
            ['name' => 'Achocolatado',],
            ['name' => 'Açucar',],
            ['name' => 'Sal',],
            ['name' => 'Corante',],
            ['name' => 'Corante',],
            ['name' => 'Maizena',],
            ['name' => 'Óleo',],
            ['name' => 'Margarina',],
            ['name' => 'Nesquik',],
            ['name' => 'Maionese Vigor',],
            ['name' => 'Maionese Heinz',],
            ['name' => 'Ketchup',],
            ['name' => 'Mostarda',],
            ['name' => 'Shoyo',],
            ['name' => 'Barbecue',],
            ['name' => 'Molho de Alho',],
            ['name' => 'Vinagre',],
            ['name' => 'Creme de Leite',],
            ['name' => 'Leite Condensado',],
            ['name' => 'Milho',],
            ['name' => 'Azeitona',],
            ['name' => 'Oregano',],

            ['name' => 'Batata',],
            ['name' => 'Coentro',],
            ['name' => 'Louro',],
            ['name' => 'Pimenta',],
            ['name' => 'Repolho',],
            ['name' => 'Alface',],
            ['name' => 'Pimentão',],
            ['name' => 'Cenoura',],
            ['name' => 'Cebola',],
            ['name' => 'Brócolis',],
            ['name' => 'Alho',],
            ['name' => 'Tomate',],
            ['name' => 'Limão',],
            ['name' => 'Laranja',],
            ['name' => 'Melão',],
            ['name' => 'Laranja',],

            ['name' => 'Poupa de frutas',],
            ['name' => 'Presunto',],
            ['name' => 'Mortadela',],
            ['name' => 'Peito de Peru',],
            ['name' => 'Queijo',],
            ['name' => 'Danone',],
            ['name' => 'Leite de Caixinha',],
            ['name' => 'Achocolatado Líquido',],
            ['name' => 'Água com Gás',],
            ['name' => 'Suco de Pacote',],
            ['name' => 'Salgadinho',],
            ['name' => 'Pizza',],
            ['name' => 'Nugget',],
            ['name' => 'Queijo Ralado',],
            ['name' => 'Sazon',],
            ['name' => 'Requeijão',],
            ['name' => 'Rap10',],
            
            ['name' => 'Ovo',],
            ['name' => 'Ovo de Codorna',],
            ['name' => 'Ovo de Codorna',],

            ['name' => 'Água Sanitária',],
            ['name' => 'Papel Higiênico',],
            ['name' => 'Desodorante',],
            ['name' => 'Sabonete',],
            ['name' => 'Sabonete Liquido',],
            ['name' => 'Pasta de Dente',],
            ['name' => 'Enxaguante Bucal',],
            ['name' => 'Shampoo',],
            ['name' => 'Condicionador',],
            ['name' => 'Desinfetante',],
            ['name' => 'Sabão em Pó',],
            ['name' => 'Sabão em Barra',],
            ['name' => 'Sabão Liquido',],
            ['name' => 'Detergente',],
            ['name' => 'Amaciante',],
            ['name' => 'Esponja',],
            ['name' => 'Veneno',],
            ['name' => 'Bombril',],
            ['name' => 'Óleo de Peroba',],
            ['name' => 'Veja',],
            ['name' => 'Acetona',],
            ['name' => 'Papel Toalha',],
            ['name' => 'Cif',],
            ['name' => 'Pato',],
            
            ['name' => 'Calabresa',],
            ['name' => 'Frango',],
            ['name' => 'Charque',],
            ['name' => 'Peito de Frango',],
            ['name' => 'Carne',],
            ['name' => 'Salsicha',],
            ['name' => 'Salame',],
        ];

        $products = $this->table('products');
        $products->truncate();
        $products->insert($data)->save();
    }
}

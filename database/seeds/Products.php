<?php

declare(strict_types=1);

use MyShoppingCart\Infrastructure\Service\UuidGenerator;
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
            ['id' => $this->generateId(), 'name' => 'Arroz',],
            ['id' => $this->generateId(), 'name' => 'Feijão Marrom',],
            ['id' => $this->generateId(), 'name' => 'Feijão Preto',],
            ['id' => $this->generateId(), 'name' => 'Macarrão Linguine',],
            ['id' => $this->generateId(), 'name' => 'Macarrão Penne',],
            ['id' => $this->generateId(), 'name' => 'Macarrão Parafuso',],
            ['id' => $this->generateId(), 'name' => 'Macarrão Rigattoni',],
            ['id' => $this->generateId(), 'name' => 'Cuscuz',],
            ['id' => $this->generateId(), 'name' => 'Tapioca',],
            ['id' => $this->generateId(), 'name' => 'Farinha de Trigo',],
            ['id' => $this->generateId(), 'name' => 'Farinha',],
            ['id' => $this->generateId(), 'name' => 'Café em Pó',],
            ['id' => $this->generateId(), 'name' => 'Leite em Pó',],
            ['id' => $this->generateId(), 'name' => 'Achocolatado',],
            ['id' => $this->generateId(), 'name' => 'Açucar',],
            ['id' => $this->generateId(), 'name' => 'Sal',],
            ['id' => $this->generateId(), 'name' => 'Corante',],
            ['id' => $this->generateId(), 'name' => 'Corante',],
            ['id' => $this->generateId(), 'name' => 'Maizena',],
            ['id' => $this->generateId(), 'name' => 'Óleo',],
            ['id' => $this->generateId(), 'name' => 'Margarina',],
            ['id' => $this->generateId(), 'name' => 'Nesquik',],
            ['id' => $this->generateId(), 'name' => 'Maionese Vigor',],
            ['id' => $this->generateId(), 'name' => 'Maionese Heinz',],
            ['id' => $this->generateId(), 'name' => 'Ketchup',],
            ['id' => $this->generateId(), 'name' => 'Mostarda',],
            ['id' => $this->generateId(), 'name' => 'Shoyo',],
            ['id' => $this->generateId(), 'name' => 'Barbecue',],
            ['id' => $this->generateId(), 'name' => 'Molho de Alho',],
            ['id' => $this->generateId(), 'name' => 'Vinagre',],
            ['id' => $this->generateId(), 'name' => 'Creme de Leite',],
            ['id' => $this->generateId(), 'name' => 'Leite Condensado',],
            ['id' => $this->generateId(), 'name' => 'Milho',],
            ['id' => $this->generateId(), 'name' => 'Azeitona',],
            ['id' => $this->generateId(), 'name' => 'Oregano',],

            ['id' => $this->generateId(), 'name' => 'Batata',],
            ['id' => $this->generateId(), 'name' => 'Coentro',],
            ['id' => $this->generateId(), 'name' => 'Louro',],
            ['id' => $this->generateId(), 'name' => 'Pimenta',],
            ['id' => $this->generateId(), 'name' => 'Repolho',],
            ['id' => $this->generateId(), 'name' => 'Alface',],
            ['id' => $this->generateId(), 'name' => 'Pimentão',],
            ['id' => $this->generateId(), 'name' => 'Cenoura',],
            ['id' => $this->generateId(), 'name' => 'Cebola',],
            ['id' => $this->generateId(), 'name' => 'Brócolis',],
            ['id' => $this->generateId(), 'name' => 'Alho',],
            ['id' => $this->generateId(), 'name' => 'Tomate',],
            ['id' => $this->generateId(), 'name' => 'Limão',],
            ['id' => $this->generateId(), 'name' => 'Laranja',],
            ['id' => $this->generateId(), 'name' => 'Melão',],
            ['id' => $this->generateId(), 'name' => 'Laranja',],

            ['id' => $this->generateId(), 'name' => 'Poupa de frutas',],
            ['id' => $this->generateId(), 'name' => 'Presunto',],
            ['id' => $this->generateId(), 'name' => 'Mortadela',],
            ['id' => $this->generateId(), 'name' => 'Peito de Peru',],
            ['id' => $this->generateId(), 'name' => 'Queijo',],
            ['id' => $this->generateId(), 'name' => 'Danone',],
            ['id' => $this->generateId(), 'name' => 'Leite de Caixinha',],
            ['id' => $this->generateId(), 'name' => 'Achocolatado Líquido',],
            ['id' => $this->generateId(), 'name' => 'Água com Gás',],
            ['id' => $this->generateId(), 'name' => 'Suco de Pacote',],
            ['id' => $this->generateId(), 'name' => 'Salgadinho',],
            ['id' => $this->generateId(), 'name' => 'Pizza',],
            ['id' => $this->generateId(), 'name' => 'Nugget',],
            ['id' => $this->generateId(), 'name' => 'Queijo Ralado',],
            ['id' => $this->generateId(), 'name' => 'Sazon',],
            ['id' => $this->generateId(), 'name' => 'Requeijão',],
            ['id' => $this->generateId(), 'name' => 'Rap10',],
            
            ['id' => $this->generateId(), 'name' => 'Ovo',],
            ['id' => $this->generateId(), 'name' => 'Ovo de Codorna',],
            ['id' => $this->generateId(), 'name' => 'Ovo de Codorna',],

            ['id' => $this->generateId(), 'name' => 'Água Sanitária',],
            ['id' => $this->generateId(), 'name' => 'Papel Higiênico',],
            ['id' => $this->generateId(), 'name' => 'Desodorante',],
            ['id' => $this->generateId(), 'name' => 'Sabonete',],
            ['id' => $this->generateId(), 'name' => 'Sabonete Liquido',],
            ['id' => $this->generateId(), 'name' => 'Pasta de Dente',],
            ['id' => $this->generateId(), 'name' => 'Enxaguante Bucal',],
            ['id' => $this->generateId(), 'name' => 'Shampoo',],
            ['id' => $this->generateId(), 'name' => 'Condicionador',],
            ['id' => $this->generateId(), 'name' => 'Desinfetante',],
            ['id' => $this->generateId(), 'name' => 'Sabão em Pó',],
            ['id' => $this->generateId(), 'name' => 'Sabão em Barra',],
            ['id' => $this->generateId(), 'name' => 'Sabão Liquido',],
            ['id' => $this->generateId(), 'name' => 'Detergente',],
            ['id' => $this->generateId(), 'name' => 'Amaciante',],
            ['id' => $this->generateId(), 'name' => 'Esponja',],
            ['id' => $this->generateId(), 'name' => 'Veneno',],
            ['id' => $this->generateId(), 'name' => 'Bombril',],
            ['id' => $this->generateId(), 'name' => 'Óleo de Peroba',],
            ['id' => $this->generateId(), 'name' => 'Veja',],
            ['id' => $this->generateId(), 'name' => 'Acetona',],
            ['id' => $this->generateId(), 'name' => 'Papel Toalha',],
            ['id' => $this->generateId(), 'name' => 'Cif',],
            ['id' => $this->generateId(), 'name' => 'Pato',],
            
            ['id' => $this->generateId(), 'name' => 'Calabresa',],
            ['id' => $this->generateId(), 'name' => 'Frango',],
            ['id' => $this->generateId(), 'name' => 'Charque',],
            ['id' => $this->generateId(), 'name' => 'Peito de Frango',],
            ['id' => $this->generateId(), 'name' => 'Carne',],
            ['id' => $this->generateId(), 'name' => 'Salsicha',],
            ['id' => $this->generateId(), 'name' => 'Salame',],
        ];

        $products = $this->table('products');
        $products->truncate();
        $products->insert($data)->save();
    }

    private function generateId(): string {
        return new UuidGenerator()->generate();
    }
}

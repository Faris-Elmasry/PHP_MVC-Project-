<?php

namespace App\Seeders;

use Elmasry\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeder
     */
    public function run(): void
    {
        $this->insert('products', [
            ['name' => 'Laptop', 'price' => 20000, 'vat' => 14],
            ['name' => 'Mouse', 'price' => 300, 'vat' => 14],
            ['name' => 'Keyboard', 'price' => 700, 'vat' => 14],
            ['name' => 'Monitor', 'price' => 4500, 'vat' => 14],
            ['name' => 'Headphones', 'price' => 1200, 'vat' => 14],
        ]);
    }
}

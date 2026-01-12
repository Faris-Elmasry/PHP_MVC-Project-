<?php

namespace App\Seeders;

use Elmasry\Database\Seeder;

class InvoiceItemsSeeder extends Seeder
{
    /**
     * Run the database seeder
     */
    public function run(): void
    {
        $this->insert('invoice_items', [
            ['invoice_id' => 1, 'product_id' => 1, 'quantity' => 1, 'price' => 20000],
            ['invoice_id' => 1, 'product_id' => 2, 'quantity' => 1, 'price' => 300],
            ['invoice_id' => 2, 'product_id' => 4, 'quantity' => 1, 'price' => 4500],
            ['invoice_id' => 3, 'product_id' => 5, 'quantity' => 1, 'price' => 1200],
            ['invoice_id' => 4, 'product_id' => 3, 'quantity' => 2, 'price' => 700],
        ]);
    }
}

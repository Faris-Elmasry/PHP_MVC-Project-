<?php

namespace App\Seeders;

use Elmasry\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders in order
     */
    public function run(): void
    {
        // Call seeders in dependency order
        $this->call([
            UsersSeeder::class,
            ProductsSeeder::class,
            InvoicesSeeder::class,
            InvoiceItemsSeeder::class,
        ]);
    }
}

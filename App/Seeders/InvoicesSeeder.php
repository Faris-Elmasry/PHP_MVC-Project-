<?php

namespace App\Seeders;

use Elmasry\Database\Seeder;

class InvoicesSeeder extends Seeder
{
    /**
     * Run the database seeder
     */
    public function run(): void
    {
        $this->insert('invoices', [
            ['user_id' => 1, 'total_amount' => 23000 , 'paid' => 1],
            ['user_id' => 2, 'total_amount' => 5000 , 'paid' => 1],
            ['user_id' => 3, 'total_amount' => 1200 , 'paid' => 0],
            ['user_id' => 4, 'total_amount' => 8000, 'paid' => 0],
            ['user_id' => 5, 'total_amount' => 300, 'paid' => 0],
        ]);
    }
}

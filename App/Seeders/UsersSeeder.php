<?php

namespace App\Seeders;

use Elmasry\Database\Seeder;
use Elmasry\Support\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeder
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('123456');

        $this->insert('users', [
            [
                'name' => 'Ahmed Ali',
                 'email' => 'ahmed@test.com',
                'password' => $defaultPassword,
                'phone' => '0100000001',
                'address' => 'Cairo'
            ],
            [
                'name' => 'Sara Mohamed',
                 'email' => 'sara@test.com',
                'password' => $defaultPassword,
                'phone' => '0100000002',
                'address' => 'Giza'
            ],
            [
                'name' => 'Omar Hassan',
                 'email' => 'omar@test.com',
                'password' => $defaultPassword,
                'phone' => '0100000003',
                'address' => 'Alex'
            ],
            [
                'name' => 'Mona Adel',
                 'email' => 'mona@test.com',
                'password' => $defaultPassword,
                'phone' => '0100000004',
                'address' => 'Tanta'
            ],
            [
                'name' => 'Youssef Samy',
                 'email' => 'youssef@test.com',
                'password' => $defaultPassword,
                'phone' => '0100000005',
                'address' => 'Mansoura'
            ],
        ]);
    }
}

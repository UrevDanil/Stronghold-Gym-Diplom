<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            AdminUserSeeder::class,
            WorkoutsSeeder::class,
            SubscriptionsSeeder::class,
            // Можно добавить тестовых клиентов
            // ClientsSeeder::class,
        ]);
    }
}
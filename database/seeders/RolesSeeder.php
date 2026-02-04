<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /*public function run()
    {
        // Очистить таблицу перед заполнением (опционально)
        Role::truncate();
        
        // Массив ролей
        $roles = [
            ['name' => 'owner', 'description' => 'Владелец клуба'],
            ['name' => 'admin', 'description' => 'Администратор'],
            ['name' => 'trainer', 'description' => 'Тренер'],
            ['name' => 'client', 'description' => 'Клиент'],
        ];
        
        // Добавить роли в БД
        foreach ($roles as $role) {
            Role::create($role);
        }
        
        $this->command->info('Роли успешно добавлены!');
    }*/
}
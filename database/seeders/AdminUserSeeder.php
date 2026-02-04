<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Найти роль администратора
        $adminRole = Role::where('name', 'admin')->first();
        
        // Создать администратора
        User::create([
            'name' => 'Администратор',
            'email' => 'Stronghold@gmail.com',
            'phone' => '+79494045353',
            'password' => Hash::make('admin123'), // Пароль: admin123
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        
        // Создать владельца
        $ownerRole = Role::where('name', 'owner')->first();
        User::create([
            'name' => 'Владелец_Данил',
            'email' => 'danilurev261@gmail.com',
            'phone' => '+79494045360',
            'password' => Hash::make('owner123'),
            'role_id' => $ownerRole->id,
            'email_verified_at' => now(),
        ]);
        
        // Создать тестового тренера
        $trainerRole = Role::where('name', 'trainer')->first();
        User::create([
            'name' => 'Константин Федоров',
            'email' => 'konstantin06072005@gmail.com',
            'phone' => '+79494135616',
            'password' => Hash::make('trainer123'),
            'role_id' => $trainerRole->id,
            'qualification' => 'Мастер спорта по пауэрлифтингу',
            'specialization' => 'Приседания со штангой, Жим лёжа, Становая тяга, Силовые тренировки',
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('Административные пользователи созданы!');
    }
}
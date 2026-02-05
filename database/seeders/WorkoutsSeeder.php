<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workout;

class WorkoutsSeeder extends Seeder
{
    /*public function run()
    {
        $workouts = [
            [
                'name' => 'Кроссфит',
                'description' => 'Высокоинтенсивные функциональные тренировки',
                'level' => 'Продвинутый',
                'duration_minutes' => 60,
                'max_participants' => 15,
                'price' => 500.00,
                'color' => '#FF6B6B',
            ],
            [
                'name' => 'Йога',
                'description' => 'Классическая хатха йога для расслабления и гибкости',
                'level' => 'Любой',
                'duration_minutes' => 75,
                'max_participants' => 20,
                'price' => 400.00,
                'color' => '#4ECDC4',
            ],
            [
                'name' => 'Пилатес',
                'description' => 'Укрепление мышечного корсета и улучшение осанки',
                'level' => 'Средний',
                'duration_minutes' => 55,
                'max_participants' => 12,
                'price' => 450.00,
                'color' => '#95E1D3',
            ],
            [
                'name' => 'TRX',
                'description' => 'Функциональный тренинг с петлями TRX',
                'level' => 'Средний',
                'duration_minutes' => 50,
                'max_participants' => 10,
                'price' => 600.00,
                'color' => '#dbeb01',
            ],
            [
                'name' => 'Силовая тренировка',
                'description' => 'Работа со свободными весами и тренажерами',
                'level' => 'Любой',
                'duration_minutes' => 80,
                'max_participants' => 8,
                'price' => 550.00,
                'color' => '#ff9a02',
            ],
        ];
        
        foreach ($workouts as $workout) {
            Workout::create($workout);
        }
        
        $this->command->info('Типы тренировок добавлены!');
    }*/
}
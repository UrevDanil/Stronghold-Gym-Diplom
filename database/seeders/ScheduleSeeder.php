<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Workout;
use App\Models\User;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        $workouts = Workout::all();
        $trainers = User::whereHas('role', function($q) {
            $q->where('name', 'trainer');
        })->get();

        if ($workouts->isEmpty() || $trainers->isEmpty()) {
            $this->command->error('Сначала создай тренировки и тренеров!');
            return;
        }

        $rooms = ['Основной зал', 'Кардио-зона', 'Зал единоборств', 'Кроссфит бокс'];
        
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            
            // Утренняя тренировка
            Schedule::create([
                'workout_id' => $workouts->random()->id,
                'trainer_id' => $trainers->random()->id,
                'date' => $date,
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'status' => 'scheduled',
                'room' => $rooms[array_rand($rooms)],
                'current_participants' => rand(0, 8),
            ]);
            
            // Дневная тренировка
            Schedule::create([
                'workout_id' => $workouts->random()->id,
                'trainer_id' => $trainers->random()->id,
                'date' => $date,
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'status' => 'scheduled',
                'room' => $rooms[array_rand($rooms)],
                'current_participants' => rand(0, 8),
            ]);
            
            // Вечерняя тренировка
            Schedule::create([
                'workout_id' => $workouts->random()->id,
                'trainer_id' => $trainers->random()->id,
                'date' => $date,
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'status' => 'scheduled',
                'room' => $rooms[array_rand($rooms)],
                'current_participants' => rand(0, 8),
            ]);
            
            // В выходные добавим еще одну
            if ($date->isWeekend()) {
                Schedule::create([
                    'workout_id' => $workouts->random()->id,
                    'trainer_id' => $trainers->random()->id,
                    'date' => $date,
                    'start_time' => '15:00:00',
                    'end_time' => '16:00:00',
                    'status' => 'scheduled',
                    'room' => $rooms[array_rand($rooms)],
                    'current_participants' => rand(0, 8),
                ]);
            }
        }
        
        $this->command->info('Расписание успешно создано!');
    }
}
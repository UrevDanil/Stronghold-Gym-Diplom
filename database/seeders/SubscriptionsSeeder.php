<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionsSeeder extends Seeder
{
    /*public function run()
    {
        $subscriptions = [
            [
                'name' => 'Разовый',
                'description' => 'Одно посещение любой тренировки',
                'price' => 500.00,
                'duration_days' => 1,
                'workouts_count' => 1,
                'type' => 'count',
            ],
            [
                'name' => 'Месячный (безлимит)',
                'description' => 'Неограниченное количество посещений на месяц',
                'price' => 5500.00,
                'duration_days' => 30,
                'workouts_count' => null,
                'type' => 'time',
            ],
            [
                'name' => '8 занятий',
                'description' => 'Пакет из 8 занятий, действует 2 месяца',
                'price' => 3500.00,
                'duration_days' => 60,
                'workouts_count' => 8,
                'type' => 'count',
            ],
            [
                'name' => '12 занятий',
                'description' => 'Пакет из 8 занятий, действует 2 месяца',
                'price' => 4500.00,
                'duration_days' => 60,
                'workouts_count' => 12,
                'type' => 'count',
            ],
            [
                'name' => 'Годовой',
                'description' => 'Неограниченное количество посещений на год',
                'price' => 50000.00,
                'duration_days' => 365,
                'workouts_count' => null,
                'type' => 'time',
            ],
        ];
        
        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }*/
}
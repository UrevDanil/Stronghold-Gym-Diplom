<?php

// app/Models/Workout.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
        'capacity',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration' => 'integer',
        'capacity' => 'integer'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function activeSchedules()
    {
        return $this->schedules()
            ->where('date', '>=', now()->toDateString())
            ->where('is_active', true);
    }
}
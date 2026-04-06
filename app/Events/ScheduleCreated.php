<?php

namespace App\Events;

use App\Models\Schedule;

class ScheduleCreated
{
    public $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }
}
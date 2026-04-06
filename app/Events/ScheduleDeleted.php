<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;

class ScheduleDeleted
{
    public $schedule;
    public $deletedBy;

    public function __construct(Schedule $schedule, User $deletedBy)
    {
        $this->schedule = $schedule;
        $this->deletedBy = $deletedBy;
    }
}
<?php

namespace App\Events;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class ScheduleUpdated
{
    use Dispatchable;

    public $schedule;
    public $updatedBy;
    public $oldData;

    public function __construct(Schedule $schedule, User $updatedBy, array $oldData = [])
    {
        $this->schedule = $schedule;
        $this->updatedBy = $updatedBy;
        $this->oldData = $oldData;
    }
}
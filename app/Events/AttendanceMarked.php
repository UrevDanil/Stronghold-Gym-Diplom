<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceMarked
{
    use Dispatchable;

    public $booking;
    public $markedBy;
    public $status;

    public function __construct(Booking $booking, User $markedBy, string $status)
    {
        $this->booking = $booking;
        $this->markedBy = $markedBy;
        $this->status = $status;
    }
}
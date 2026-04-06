<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;

class ClientBookedWorkout
{
    use Dispatchable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }
}
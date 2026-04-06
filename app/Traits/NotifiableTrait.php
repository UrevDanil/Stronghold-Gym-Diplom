<?php

namespace App\Traits;

use App\Services\NotificationService;

trait NotifiableTrait
{
    protected function notify()
    {
        return app(NotificationService::class);
    }
}
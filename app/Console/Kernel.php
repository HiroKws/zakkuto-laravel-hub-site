<?php

namespace App\Console;

use App\HubConnections\Commands\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Reminder::class,
    ];

    protected function schedule(Schedule $schedule)
    {
    }
}

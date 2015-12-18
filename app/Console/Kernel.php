<?php

namespace App\Console;

use App\HubConnections\Commands\MailCheck;
use App\HubConnections\Commands\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Reminder::class,
        MailCheck::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command('hub:mailcheck')
            ->everyTenMinutes();
    }
}

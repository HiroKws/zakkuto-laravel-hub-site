<?php

namespace App\Console;

use App\HubConnections\Commands\SiteCheck;
use App\Console\Commands\TrelloId;
use App\HubConnections\Commands\CheckRss;
use App\HubConnections\Commands\CheckTrello;
use App\HubConnections\Commands\MailCheck;
use App\HubConnections\Commands\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Reminder::class,
        MailCheck::class,
        CheckTrello::class,
        CheckRss::class,
        TrelloId::class,
        SiteCheck::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command('hub:mailcheck')
            ->everyTenMinutes();
    }
}

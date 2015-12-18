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
        $schedule
            ->command('hub:reminder  "燃えるゴミ、鳥よけカゴ出し"')
            ->dailyAt('08:00')
            ->days(1, 3, 5); // 月水金、配列でもOK
        $schedule
            ->command('hub:reminder  "川瀬さんの誕生日、プレゼントは現金でOK!Paypal可！！"')
            ->cron('0 8 4 7 *');
    }
}

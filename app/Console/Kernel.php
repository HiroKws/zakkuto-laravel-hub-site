<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * アプリケーションで提供するArtisanコマンド.
     *
     * @var array
     */
    protected $commands = [
        Commands\LogMessage::class,
    ];

    /**
     * アプリケーションのコマンド実行スケジュール定義.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('logmessage 毎分起動')
            ->cron('* * * * *');
        $schedule->command('logmessage 5分毎に起動')
            ->everyFiveMinutes();
        $schedule->command('logmessage 10分毎に起動')
            ->everyTenMinutes();
        $schedule->command('logmessage 30分毎に起動')
            ->everyThirtyMinutes();
        $schedule->command('logmessage 15分毎に起動')
            ->cron('*/15 * * * *');
        $schedule->command('logmessage 20分毎に起動')
            ->cron('*/20 * * * *');
    }
}

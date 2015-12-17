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
        $schedule->command('logmessage タスク1')
            ->cron('* * * * *');
        $schedule->command('logmessage タスク2')
            ->cron('* * * * *');
        $schedule->command('logmessage タスク3')
            ->cron('* * * * *');
    }
}

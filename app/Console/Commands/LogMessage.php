<?php

namespace App\Console\Commands;

use Log;

class LogMessage extends BaseCommand
{
    protected $signature = 'logmessage '
        .'{message=logmessage Artisan command executed. : Message to log}';

    protected $description = 'Log a message';

    public function handle()
    {
        Log::info($this->argument('message'));

        return 0; // Exitコード、正常終了
    }
}

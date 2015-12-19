<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Inspire extends Command
{
    use DispatchesJobs;

    /**
     * コンソールコマンドの識別名
     *
     * @var string
     */
    protected $signature = 'inspire';

    /**
     * コンソールコマンドの説明
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * コンソールコマンドの実行
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dispatch(new \App\Jobs\TestJob());
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{yoisho* : ヨイショの言葉。}';

    protected $description = 'はじめの一歩、Hello Worldを出力する。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach ($this->argument('yoisho') as $word) {
            echo $word.'、あっよいしょ！'.PHP_EOL;
        }
    }
}

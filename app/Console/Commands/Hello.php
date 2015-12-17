<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{name : 挨拶する人や物。} '
        .'{greeting=さん、ようこそ : 挨拶の言葉。}';

    protected $description = 'はじめの一歩、Hello Worldを出力する。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        echo $this->argument('name').$this->argument('greeting').PHP_EOL;
    }
}

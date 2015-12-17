<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{--N|name=世界 : 相手の名前}';

    protected $description = '丁寧なあいさつ。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        echo $this->option('name').'殿、ご無事で何より。'.PHP_EOL;
    }
}

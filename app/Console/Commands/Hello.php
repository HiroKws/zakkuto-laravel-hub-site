<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{--N|name=世界 : 誰に向かって、挨拶してるんじゃ。}';

    protected $description = 'はじめの一歩、Hello Worldを出力する。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        echo 'ちやーす、'.$this->option('name').'さん。'.PHP_EOL;
    }
}

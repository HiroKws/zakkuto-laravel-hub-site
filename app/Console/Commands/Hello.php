<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello';

    protected $description = 'はじめの一歩、Hello Worldを出力する。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        echo 'Hello World!!'.PHP_EOL;
    }
}
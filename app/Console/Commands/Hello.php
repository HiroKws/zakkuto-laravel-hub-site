<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{--j|japanese : 日本語表示オプション。}';

    protected $description = 'はじめの一歩、Hello Worldを出力する。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('japanese')) {
            echo 'こんにちは、世界さん！'.PHP_EOL;
        } else {
            echo 'Hello, World!!'.PHP_EOL;
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hello extends Command
{
    protected $signature = 'hello:world '
        .'{--a|add=* : 加算する数字。}';

    protected $description = '足し算計算機。';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $total = 0;
        foreach ($this->option('add') as $value) {
            $total += $value;
        }
        echo '合計：'.$total.PHP_EOL;
    }
}

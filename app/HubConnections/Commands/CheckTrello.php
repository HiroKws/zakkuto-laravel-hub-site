<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\TrelloListChecker as Notifire;

class CheckTrello extends BaseCommand
{

    protected $signature = 'hub:checktrello';

    protected $description = 'Check changes on a specific Trello list.';

    /** @var Notifire **/
    private $notifire;

    public function __construct(Notifire $notifire)
    {
        $this->notifire = $notifire;

        parent::__construct();
    }

    public function handle()
    {
        // 必要な場合、ここで引数やオプションのチェックを行う

        // リストの変化をチェック
        if ($this->notifire->run() === false)
        {
            return 1; // 異常終了（非０）
        }

        return 0; // 正常終了
    }
}

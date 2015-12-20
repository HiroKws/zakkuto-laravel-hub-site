<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\CopyEverydayTasks as Notifire;

/**
 * Wunderlistの毎日実行リスト上のタスクをコピーするコマンド
 */
class CopyEverydayTasks extends BaseCommand
{
    protected $signature = 'hub:copyeverydaytasks';

    protected $description = 'Copy everyday task on Wunderlist.';

    /** @var Notifire * */
    private $notifire;

    public function __construct(Notifire $notifire)
    {
        $this->notifire = $notifire;

        parent::__construct();
    }

    public function handle()
    {
        $this->notifire->run();

        // 終了コード
        return 0;
    }
}

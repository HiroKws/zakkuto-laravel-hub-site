<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\TaskController as Notifire;

class DoTasks extends BaseCommand
{

    protected $signature = 'hub:dotasks';

    protected $description = 'Execute task card on the Trello list.';

    /** @var Notifire **/
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

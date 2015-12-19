<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Jobs\TaskContollerJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DoTasks extends BaseCommand
{
    use DispatchesJobs; // これにより$this->dispatchが使えるようになる

    protected $signature = 'hub:dotasks';

    protected $description = 'Execute task card on the Trello list.';

    /** @var TaskContollerJob * */
    private $job;

    public function __construct(TaskContollerJob $job)
    {
        $this->job = $job;

        parent::__construct();
    }

    public function handle()
    {
        // 必要な場合、ここで引数やオプションのチェックを行う
        // リストの変化チェックロジックをキューへ投入
        $this->dispatch($this->job);

        // 終了コード
        return 0;
    }
}

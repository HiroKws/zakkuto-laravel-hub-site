<?php

namespace App\HubConnections\Jobs;

use App\HubConnections\Notifiers\TaskController as Notifire;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TaskContollerJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /** @var Notifire * */
    private $notifire;

    /**
     * コンストラクター
     */
    public function __construct()
    {
    }

    /**
     * キュー投入時に実行されるメソッド
     */
    public function handle(Notifire $notifire)
    {
        $notifire->run();
    }
}

<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\CopyTasksKicked as Event;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * 毎日のタスクコピー起動通知ロジッククラス
 */
class CopyEverydayTasks
{
    /** @var Dispatcher * */
    private $dispacher;

    /** @var Event * */
    private $event;

    /**
     * コンストラクター
     *
     * @param Dispatcher $dispatcher
     * @param Event $event
     */
    public function __construct(Dispatcher $dispatcher, Event $event)
    {
        $this->dispacher = $dispatcher;
        $this->event     = $event;
    }

    /**
     * リマインダー通知ビジネスロジック
     *
     * @param string $message
     */
    public function run()
    {
        // イベント発行
        $this->dispacher->fire($this->event);
    }
}

<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\Reminder as Event;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * リマインダー通知ロジッククラス
 */
class Reminder
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
    public function run($message)
    {
        // イベントにメッセージを設定
        $this->event->message = $message;

        // イベント発行
        $this->dispacher->fire($this->event);
    }
}

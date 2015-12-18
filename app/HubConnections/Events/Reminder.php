<?php

namespace App\HubConnections\Events;

/**
 * リマインダーイベント
 */
class Reminder extends HubConnectionBaseEvent
{
    /** @var string * */
    public $message = '';

    /**
     * イベントの文字列変換
     *
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }
}

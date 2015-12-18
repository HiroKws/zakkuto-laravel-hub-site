<?php

namespace App\HubConnections\Events;

/**
 * カード削除イベント
 */
class CardDeleted extends HubConnectionBaseEvent
{
    /** @var string **/
    public $id;

    public function __toString()
    {
        return "カード削除\n"
            .'ID：'.$this->id."\n";
    }
}

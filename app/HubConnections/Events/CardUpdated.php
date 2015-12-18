<?php

namespace App\HubConnections\Events;

/**
 * カード更新イベント
 */
class CardUpdated extends HubConnectionBaseEvent
{
    /** @var Carbon */
    public $time;

    /** @var string **/
    public $name;

    /** @var string **/
    public $id;

    public function __toString()
    {
        return "カード更新\n"
            .'ID：'.$this->id."\n"
            .'日時：'.$this->time->toDateTimeString()."\n"
            .'内容：'.$this->name."\n";
    }
}

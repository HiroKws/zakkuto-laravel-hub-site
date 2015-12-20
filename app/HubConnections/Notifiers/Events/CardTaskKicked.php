<?php

namespace App\HubConnections\Events;

use App\HubConnections\Events\HubConnectionBaseEvent;
use Carbon\Carbon;

class CardTaskKicked extends HubConnectionBaseEvent
{
    /** @var Carbon */
    public $startedTime;

    /** @var string **/
    public $name;

    /** @var string **/
    public $id;

    /** @var string **/
    public $task;

    public function __toString()
    {
        return 'タスク起動：'.$this->name."\n"
            .'起動時間：'.$this->startedTime->toDateTimeString()."\n"
            .'タスク：'.$this->task."\n"
            .'カードID：'.$this->id;
    }
}

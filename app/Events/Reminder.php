<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class Reminder extends Event
{
    use SerializesModels;

    public $time;

    public $eventName;

    public function __construct($time = null, $eventName = null)
    {
        $this->time      = $time;
        $this->eventName = $eventName;
    }
}

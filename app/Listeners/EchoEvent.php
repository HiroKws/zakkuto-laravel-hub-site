<?php

namespace App\Listeners;

use App\Events\Event;

class EchoEvent
{
    public function handle(Event $event)
    {
        echo __($event);
    }
}

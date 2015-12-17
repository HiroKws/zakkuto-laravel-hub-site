<?php

namespace App\Listeners;

use App\Events\Event;

class EchoEvent2
{
    public function handle(Event $event)
    {
        echo $event->time
            ->toDateTimeString()
        .'に'
        .$event->message
        ."の予定があります。\n";
    }
}

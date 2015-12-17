<?php

namespace App\Listeners;

use App\Events\Reminder;

class EchoEvent2
{
    public function handle(Reminder $event)
    {
        echo $event->time
            ->toDateTimeString()
        .'に'
        .$event->message
        ."の予定があります。\n";
    }
}

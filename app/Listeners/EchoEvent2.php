<?php

namespace App\Listeners;

use App\Events\ReminderInterface;

class EchoEvent2
{
    public function handle(ReminderInterface $event)
    {
        echo $event->time
            ->toDateTimeString()
        .'に'
        .$event->message
        ."の予定があります。\n";
    }
}

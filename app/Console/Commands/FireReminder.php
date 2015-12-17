<?php

namespace App\Console\Commands;

use App\Events\Reminder;
use App\Events\Reminder2;
use Carbon\Carbon;
use Config;
use Event;

class FireReminder extends BaseCommand
{
    protected $signature = 'fire:reminder '
        .'{message : Reminder message} '
        .'{time=Now : Scheduled Time(YY-MM-DD HH:MM:SS)} '
        .'{--2|2nd : Fire Reminder2 event}';

    protected $description = 'Fire Reminder event.';

    public function handle(Reminder $reminder, Reminder2 $reminder2)
    {
        $event = $this->option('2nd') ? $reminder2 : $reminder;

        $event->message = $this->argument('message');

        $time          = $this->argument('time');
        $scheduledTime = $time === 'Now'
            ? Carbon::now()
            : Carbon::createFromFormat('Y-m-d H:i:s', $time, Config::get('app.timezone'));
        $event->time = $scheduledTime;

        Event::fire($event);
    }
}

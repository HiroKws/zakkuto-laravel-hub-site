<?php

namespace App\Console\Commands;

use App\Events\Reminder;
use Carbon\Carbon;
use Event;

class FireReminder extends BaseCommand
{
    protected $signature = 'fire:reminder '
        .'{message : Reminder message} '
        .'{time=Now : Scheduled Time(YY-MM-DD HH:MM:SS)}';

    protected $description = 'Fire Reminder event.';

    public function handle(Reminder $reminder)
    {
        $reminder->message = $this->argument('message');

        $time          = $this->argument('time');
        $scheduledTime = $time === 'Now'
            ? Carbon::now()
            : Carbon::createFromFormat('Y-m-d H:i:s', $time);
        $reminder->time = $scheduledTime;

        Event::fire($reminder);
    }
}

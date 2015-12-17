<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;

class Reminder2 extends Event implements ReminderInterface
{
    use SerializesModels;

    /** @var Carbon 時間 * */
    public $time;

    /** @var string メッセージ * */
    public $message;

    public function __toString()
    {
        return '日時：'.$this->time->toDateTimeString()
            ."\n"
            .'内容：'.$this->message
            ."\n";
    }
}

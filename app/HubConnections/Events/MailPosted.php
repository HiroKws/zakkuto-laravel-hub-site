<?php

namespace App\HubConnections\Events;

use Carbon\Carbon;

class MailPosted extends HubConnectionBaseEvent
{
    /** @var string **/
    public $from;

    /** @var Carbon **/
    public $date;

    /** @var string **/
    public $subject;

    /** @var string **/
    public $body;

    public function __toString()
    {
        return '送信元：'.$this->from."\n"
            .'日時：'.$this->date."\n"
            .'タイトル：'.$this->subject."\n"
            ."内容：\n".$this->body."\n";
    }
}

<?php

namespace App\HubConnections\Events\SiteMonitoring;

use Carbon\Carbon;

class SiteUpped extends MonitorBaseEvent
{
    /** @var Carbon **/
    public $time;

    /** @var string **/
    public $url;

    public function __toString()
    {
        return "サイト復活\n"
            .$this->time->toDateTimeString()."\n"
            .$this->url."\n";
    }
}

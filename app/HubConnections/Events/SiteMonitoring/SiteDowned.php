<?php

namespace App\HubConnections\Events\SiteMonitoring;

use Carbon\Carbon;

class SiteDowned extends MonitorBaseEvent
{
    /** @var Carbon **/
    public $time;

    /** @var string **/
    public $url;

    /** @var string **/
    public $code;

    public function __toString()
    {
        return "サイトダウン\n"
            .$this->time->toDateTimeString()."\n"
            .$this->url.'(Code:'.$this->code.")\n";
    }
}

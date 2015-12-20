<?php

namespace App\HubConnections\Events;

class CopyTasksKicked extends HubConnectionBaseEvent
{
    public function __toString()
    {
        return 'Ordered to copy everyday tasks on Wunderlist.';
    }
}

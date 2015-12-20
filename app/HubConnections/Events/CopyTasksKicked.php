<?php

namespace App\HubConnections\Events;

/**
 * Wunderlist、毎日のタスクコピー起動イベント
 */
class CopyTasksKicked extends HubConnectionBaseEvent
{
    public function __toString()
    {
        return 'Ordered to copy everyday tasks on Wunderlist.';
    }
}

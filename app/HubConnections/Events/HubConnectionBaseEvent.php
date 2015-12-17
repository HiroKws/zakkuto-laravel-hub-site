<?php

namespace App\HubConnections\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * ハブサービスで使用するイベントのベースクラス.
 */
abstract class HubConnectionBaseEvent extends Event implements StringizableInterface
{
    use SerializesModels;
}

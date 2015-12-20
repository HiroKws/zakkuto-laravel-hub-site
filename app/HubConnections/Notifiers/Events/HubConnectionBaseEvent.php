<?php

namespace App\HubConnections\Events;

use App\Events\Event;
use App\HubConnection\Exceptions\RuntimeException;
use Illuminate\Queue\SerializesModels;

/**
 * ハブサービスで使用するイベントのベース抽象クラス
 *
 * __toStringメソッドを実装するように強制
 */
abstract class HubConnectionBaseEvent extends Event implements StringizableInterface
{
    use SerializesModels;

    public function __toString()
    {
        throw new \RuntimeException('Not impliment __toString magic method.');
    }
}

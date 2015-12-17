<?php

namespace App\HubConnections\Events;

/**
 * 文字列化可能であることを保証するインターフェイス.
 */
interface StringizableInterface
{
    public function __toString();
}

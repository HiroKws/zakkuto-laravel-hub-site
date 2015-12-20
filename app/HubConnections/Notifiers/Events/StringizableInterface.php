<?php

namespace App\HubConnections\Events;

/**
 * 文字列化可能であることを保証するインターフェイス
 *
 * __toStringメソッドの実装を強制
 */
interface StringizableInterface
{
    public function __toString();
}

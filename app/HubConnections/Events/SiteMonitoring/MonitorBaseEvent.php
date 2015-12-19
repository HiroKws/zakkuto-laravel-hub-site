<?php

namespace App\HubConnections\Events\SiteMonitoring;

use App\HubConnections\Events\HubConnectionBaseEvent;

/**
 * モニタリング状態変化通知イベント用ベースクラス
 */
abstract class MonitorBaseEvent extends HubConnectionBaseEvent
    implements MonitorEventInterface
{
}

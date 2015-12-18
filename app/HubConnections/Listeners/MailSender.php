<?php

namespace App\HubConnections\Listeners;

use App\HubConnections\Events\HubConnectionBaseEvent;

class MailSender
{
    /**
     * 受け取ったイベントをメールで送信する
     */
    public function handle(HubConnectionBaseEvent $event)
    {
        // 実行確認のため、ダミーコードとしてログ出力する
        \Log::info($event);
    }
}

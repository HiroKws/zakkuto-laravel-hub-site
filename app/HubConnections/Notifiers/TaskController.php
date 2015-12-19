<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\CardTaskKicked;
use App\Services\JsonArrayConverter;
use App\Services\JsonGetter;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * タスクカード実行ロジッククラス
 */
class TaskController
{
    /** @var Dispatcher * */
    private $dispatcher;

    public function __construct(
        JsonGetter $getter,
        JsonArrayConverter $converter,
        Dispatcher $dispatcher
        ) {
        $this->getter     = $getter;
        $this->converter  = $converter;
        $this->dispatcher = $dispatcher;
    }

    /**
     * タスク実行イベント通知ビジネスロジック
     */
    public function run()
    {
        // URL生成
        $url = 'https://trello.com/1/lists/'.env('TRELLO_BATCH_KICK_UP_LIST').'/cards'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN');

        // Trelloよりカード情報取得
        if (false === ($json = $this->getter->get($url))) {
            return;
        }

        // 配列へ変換
        if (is_null($cards = $this->converter->convert($json))) {
            return;
        }

        $event = new CardTaskKicked();

        foreach ($cards as $card) {
            $event->name        = $card['name'];
            $event->id          = $card['id'];
            $event->startedTime = Carbon::now();
            $event->task        = $card['desc'];

            $this->dispatcher->fire($event);
        }
    }
}

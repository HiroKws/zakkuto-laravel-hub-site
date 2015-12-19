<?php

namespace App\HubConnections\Listeners;

use App\HubConnections\Events\CardTaskKicked;
use App\Services\CommandExecutor as Executor;
use App\Services\JsonArrayConverter as Converter;
use App\Services\JsonPoster;
use App\Services\JsonPutter;

/**
 * Trelloカード上のタスク実行リスナー
 */
class CardTaskExecutor
{
    /** @var JsonPutter */
    private $putter;

    /** @var JsonPoster */
    private $poster;

    /** @var Converter */
    private $converter;

    /** @var Executor */
    private $executor;

    public function __construct(
        JsonPutter $putter,
        JsonPoster $poster,
        Converter $converter,
        Executor $executor
        ) {
        $this->putter    = $putter;
        $this->poster    = $poster;
        $this->converter = $converter;
        $this->executor  = $executor;
    }

    /**
     * 受け取ったイベントの内容を実行する
     *
     * @param  MonitorableInterface  $event
     * @return void
     */
    public function handle(CardTaskKicked $event)
    {
        // 実行中リストへ移動
        $url = 'https://trello.com/1/cards/'.$event->id.'/idList'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN')
            .'&value='.env('TRELLO_BATCH_EXECUTING_LIST');

        $updatedCard = $this->putter->put($url);

        // コマンド実行、実行コード(通常正常時0)が返ってくる
        $result = $this->executor->execute($event->task);

        // エラーの場合のみ、コメントとして実行結果を追加
        if ($result !== 0) {
            // メッセージをログしておく
            \Log::alert($this->executor->getMessage());

            // コメント長の制限は1から16384だが、文字数かバイト数か不明
            // そのためコメント長は未チェック
            $url = 'https://trello.com/1/cards/'.$event->id.'/actions/comments'
                .'?key='.env('TRELLO_KEY')
                .'&token='.env('TRELLO_TOKEN')
                .'&text='.urlencode($this->executor->getMessage());

            $updatedCard = $this->poster->post($url);
        }

        // 実行を終えたので、待機リストへ移動
        $url = 'https://trello.com/1/cards/'.$event->id.'/idList'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN')
            .'&value='.env('TRELLO_BATCH_WAIT_STACK_LIST');

        $updatedCard = $this->putter->put($url);
    }
}

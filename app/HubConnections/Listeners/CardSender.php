<?php

namespace App\HubConnections\Listeners;

use App\HubConnections\Events\HubConnectionBaseEvent;
use App\Services\JsonPoster;

class CardSender
{
    /** @var JsonPoster */
    private $poster;

    public function __construct(JsonPoster $poster)
    {
        $this->poster = $poster;
    }

    /**
     * 受け取ったイベントをTrelloのリストにログする
     *
     * @param  HubConnectionBaseEvent  $event
     */
    public function handle(HubConnectionBaseEvent $event)
    {
        $url = 'https://trello.com/1/cards/'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN')
            .'&idList='.env('TRELLO_LOG_LIST_ID')
            .'&name='.urlencode($event);

        // 生成したカード情報がJSONで返ってくるが、今回は使用しない
        $this->poster->post($url);
    }
}

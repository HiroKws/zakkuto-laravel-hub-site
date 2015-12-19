<?php

namespace App\HubConnections\Listeners\SiteMonitoring;

use App\HubConnections\Events\SiteMonitoring\MonitorBaseEvent;
use App\HubConnections\Events\SiteMonitoring\SiteDowned;
use App\HubConnections\Events\SiteMonitoring\SiteUpped;
use App\Services\JsonArrayConverter as Converter;
use App\Services\JsonGetter;
use App\Services\JsonPoster;
use App\Services\JsonPutter;

/**
 * Trelloリスト上のサイト監視結果更新リスナー
 */
class MonitoringUpdater
{
    /** @var JosnGetter */
    private $getter;

    /** @var JsonPoster */
    private $poster;

    /** @var Putter */
    private $putter;

    /** @var Converter */
    private $converter;

    public function __construct(JsonGetter $getter, JsonPoster $poster, JsonPutter $putter, Converter $converter)
    {
        $this->getter    = $getter;
        $this->poster    = $poster;
        $this->putter    = $putter;
        $this->converter = $converter;
    }

    /**
     * 受け取ったイベントをTrelloのリストにログする
     *
     * @param  MonitorableInterface  $event
     * @return void
     */
    public function handle(MonitorBaseEvent $event)
    {
        // サイト状態リスト上のカードを取得
        $url = 'https://trello.com/1/lists/'.env('TRELLO_SITES_STATUS_LIST').'/cards'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN');

        if (false === ($result = $this->getter->get($url))) {
            \Log::notice('TrelloからWebサイト監視リストの情報が取得できませんでした。');
            return;
        }

        $cards = $this->converter->convert($result);

        foreach ($cards as $card) {
            if ($card['name'] === $event->url) {
                $updateCard = $card;
                break;
            }
        }

        if (isset($updateCard)) {
            // 既存ラベル色取得、緑と赤は削除
            $labels = array_diff(array_column($updateCard['labels'], 'color'),
                ['green', 'red']);
            $labelString = implode(',', $labels);

            // 既存カード更新
            if ($event instanceof SiteUpped) {
                // サイト復活時
                // ラベル色緑設定
                $url = 'https://trello.com/1/cards/'.$updateCard['id'].'/labels'
                    .'?key='.env('TRELLO_KEY')
                    .'&token='.env('TRELLO_TOKEN')
                    .'&value='.trim($labelString.',green', ',');

                $result = $this->putter->put($url);

                // 説明文からダウン時間情報削除
                $url = 'https://trello.com/1/cards/'.$updateCard['id'].'/desc'
                    .'?key='.env('TRELLO_KEY')
                    .'&token='.env('TRELLO_TOKEN')
                    .'&value='.urlencode(trim(preg_replace('/<.+より停止中 >/u', '',
                                $updateCard['desc'])));

                $result = $this->putter->put($url);
            } else {
                // サイトダウン時
                // ラベル色赤設定
                $url = 'https://trello.com/1/cards/'.$updateCard['id'].'/labels'
                    .'?key='.env('TRELLO_KEY')
                    .'&token='.env('TRELLO_TOKEN')
                    .'&value='.trim($labelString.',red', ',');

                $result = $this->putter->put($url);

                // 説明文にダウン時間情報追加
                $url = 'https://trello.com/1/cards/'.$updateCard['id'].'/desc'
                    .'?key='.env('TRELLO_KEY')
                    .'&token='.env('TRELLO_TOKEN')
                    .'&value='.urlencode('< '
                        .$event->time->toDateTimeString()
                        ." より停止中 >\n".$updateCard['desc']);

                $result = $this->putter->put($url);
            }
        } else {
            // 新規カード追加
            $url = 'https://trello.com/1/cards/'
                .'?key='.env('TRELLO_KEY')
                .'&token='.env('TRELLO_TOKEN')
                .'&idList='.env('TRELLO_SITES_STATUS_LIST')
                .'&name='.urlencode($event->url)
                .'&labels='.($event instanceof SiteUpped ? 'green' : 'red');

            $newCard = $this->converter->convert($this->poster->post($url));

            if ($event instanceof SiteDowned) {
                // 説明文のダウン時間情報更新
                $url = 'https://trello.com/1/cards/'.$newCard['id'].'/desc'
                    .'?key='.env('TRELLO_KEY')
                    .'&token='.env('TRELLO_TOKEN')
                    .'&value='.urlencode('< '
                        .$event->time->toDateTimeString()
                        ." より停止中 >\n");

                $result = $this->putter->put($url);
            }
        }
    }
}

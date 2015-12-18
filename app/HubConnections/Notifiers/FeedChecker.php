<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\FeedPosted;
use App\Services\PriorDateReader as Reader;
use Carbon\Carbon;
use Exception;
use FastFeed\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem as File;
use Log;

class FeedChecker
{
    /** @var File **/
    private $file;

    /** @var Reader **/
    private $reader;

    /** @var Dispatcher **/
    private $dispatcher;

    public function __construct(
        File $file,
        Reader $reader,
        Dispatcher $dispatcher
    ) {
        $this->file       = $file;
        $this->reader     = $reader;
        $this->dispatcher = $dispatcher;
    }

    public function run($url)
    {
        // 前回のデータを取得
        $lastFeedFilename = storage_path().$this->convertUrlToFileName($url);
        $lastFeedTime     = $this->reader->read($lastFeedFilename, '2000-01-01 00:00:00');

        // 保存していた最終アイテムの投稿時間を取得
        $lastTime = Carbon::createFromFormat('Y-m-d H:i:s', trim($lastFeedTime), 'Asia/Tokyo');

        // RSS取得
        try {
            $fastFeed = Factory::create();
            $fastFeed->addFeed('default', $url);
            $items = $fastFeed->fetch('default');
        } catch (Exception $e) {
            Log::error('RSS取得失敗。'.$e->getMessage());
            return false;
        }

        // 取得できなければ終了
        if (empty($items)) {
            return false;
        }

        $event = new FeedPosted();

        foreach ($items as $item) {
            // 以前の最新フィードより新しい物だけを処理
            $itemTime = Carbon::instance($item->getDate());
            if ($itemTime->gt($lastTime)) {
                // 取得情報をイベントへ！！
                $event->author  = $item->getAuthor();
                $event->content = html_entity_decode($item->getContent());
                $event->date    = Carbon::instance($item->getDate());
                $event->id      = $item->getId();
                $event->image   = $item->getImage();
                $event->intro   = html_entity_decode($item->getIntro());
                $event->name    = html_entity_decode($item->getName());
                $event->source  = $item->getSource();
                $event->tags    = $item->getTags();

                // イベント発行
                $this->dispatcher->fire($event);
            }
        }

        if (count($items) >= 1) {
            // 最新アイテムの投稿時間を
            // 目認しやすいように日本時間で保存する
            $latestTime = Carbon::instance(head($items)->getDate());

            $this->file->put($lastFeedFilename, $latestTime
                ->timezone('Asia/Tokyo')
                ->toDateTimeString());
        }

        return true;
    }

    private function convertUrlToFileName($url)
    {
        return '/feed-'.strtr($url, ':/', '--').'.txt';
    }
}

<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\CardCreated;
use App\HubConnections\Events\CardDeleted;
use App\HubConnections\Events\CardUpdated;
use App\Services\JsonArrayConverter;
use App\Services\JsonGetter;
use App\Services\PriorDateReader as Reader;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem as File;

class TrelloListChecker
{
    /** @var JsonGetter **/
    private $getter;

    /** @var JsonArrayConverter **/
    private $converter;

    /** @var File **/
    private $file;

    /** @var Reader **/
    private $reader;

    /** @var Dispatcher **/
    private $dispatcher;

    /** @var string カード情報保存ファイル名 **/
    private $lastDataFile = '/cards-data-on-check-list.json';

    public function __construct(
        JsonGetter $getter,
        JsonArrayConverter $converter,
        File $file,
        Reader $reader,
        Dispatcher $dispatcher
    ) {
        $this->getter     = $getter;
        $this->converter  = $converter;
        $this->file       = $file;
        $this->reader     = $reader;
        $this->dispatcher = $dispatcher;
    }

    public function run()
    {
        // 前回のデータを取得
        $oldCards = $this->converter->convert(
            $this->reader->read(storage_path().$this->lastDataFile, '[]'));

        if (is_null($oldCards)) {
            return false;
        }

        // URL生成
        $url = 'https://trello.com/1/lists/'.env('TRELLO_LIST_ID').'/cards'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN');

        // Trelloよりカード情報取得
        if (false === ($json = $this->getter->get($url))) {
            return false;
        }

        // 取得したカード情報（JSON形式）を配列へ変換
        if (is_null($cards = $this->converter->convert($json))) {
            return false;
        }

        $created      = new CardCreated();
        $updated      = new CardUpdated();
        $currentCards = [];

        foreach ($cards as $card) {
            // 保存用データ作成
            $id = $card['id'];
            // 日本時間で保存したほうが分かりやすい
            $carbonTime = (new Carbon($card['dateLastActivity']))
                ->timezone('Asia/Tokyo');
            $timeString        = $carbonTime->toDateTimeString();
            $currentCards[$id] = $timeString;

            // 直前のデーターに存在していなければ新規カード
            if (!array_key_exists($id, $oldCards)) {
                $created->id   = $id;
                $created->name = $card['name'];
                $created->time = $carbonTime;

                $this->dispatcher->fire($created);

                continue;
            }

            // IDが一致するのに最終更新時間が異なっていれば更新
            if ($timeString !== $oldCards[$id]) {
                $updated->id   = $id;
                $updated->name = $card['name'];
                $updated->time = $carbonTime;

                $this->dispatcher->fire($updated);
            }
        }

        $deleted = new CardDeleted();

        // 削除されたカードを選択
        $deletedCards = array_diff_key($oldCards, $currentCards);

        foreach ($deletedCards as $deletedId => $time) {
            $deleted->id = $deletedId;

            $this->dispatcher->fire($deleted);
        }

        // 今回の取得データをファイルへ保存
        $this->file->put(storage_path().$this->lastDataFile,
            json_encode($currentCards));

        return true;
    }
}

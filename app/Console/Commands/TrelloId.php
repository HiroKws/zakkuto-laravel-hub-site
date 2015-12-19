<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use App\Services\JsonArrayConverter as Converter;
use App\Services\JsonGetter;
use Carbon\Carbon;

class TrelloId extends BaseCommand
{
    protected $signature = 'trello:id '
        .'{--b|board : Show all board id} '
        .'{--l|list : Show all list id} '
        .'{--c|card : Show all card id}';

    protected $description = "Show trello resources' id.";

    /** @var JsonGetter */
    private $getter;

    /** @var Converter */
    private $converter;

    /** @var Carbon */
    private $benchTime;

    private $apiCall;

    private $myBoards;

    private $myLists;

    private $myCards;

    public function __construct(JsonGetter $getter, Converter $converter)
    {
        $this->getter    = $getter;
        $this->converter = $converter;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // オプション指定省略時は全部表示
        $opt = $this->option();
        if (!($opt['board'] || $opt['list'] || $opt['card'])) {
            $opt['board'] = $opt['list'] = $opt['card'] = true;
        }

        // ボード取得
        if ($opt['board']) {
            $boards = $this->getAllBoards();

            $this->line(__("<comment>\nボード：\n</comment>"));

            foreach ($boards as $id => $name) {
                $this->line(__($name.'<info>(Id: '.$id.')</info>'));
            }
        }

        // リスト取得
        if ($opt['list']) {
            $lists = $this->getAllLists();

            $this->line(__("<comment>\nリスト：\n</comment>"));

            foreach ($lists as $id => $list) {
                $this->line(__($list['name']
                        .'<info>(Id: '.$id
                        .' Board Name: '.$list['boardName']
                        .' Board Id: '.$list['boardId']
                        .' )</info>'));
            }
        }

        // カード取得
        if ($opt['card']) {
            $cards = $this->getAllCards();

            $this->line(__("<comment>\nカード：\n</comment>"));

            foreach ($cards as $id => $card) {
                $this->line(__($card['name']
                        .'<info>(Id: '.$id
                        .' List Name: '.$card ['listName']
                        .' List Id: '.$card['listId']
                        .' Board Name: '.$card['boardName']
                        .' Board Id: '.$card['boardId']
                        .' )</info>'));
            }
        }
    }

    private function getAllBoards()
    {
        if (!is_null($this->myBoards)) {
            return $this->myBoards;
        }

        $url = 'https://trello.com/1/members/me/boards'
            .'?key='.env('TRELLO_KEY')
            .'&token='.env('TRELLO_TOKEN');

        if (false === ($result = $this->getter->get($url))) {
            return false;
        }

        $boads = $this->converter->convert($result);

        return $this->myBoards = array_combine(
            array_column($boads, 'id'), array_column($boads, 'name'));
    }

    private function getAllLists()
    {
        $boards = $this->getAllBoards();

        $this->initializeGentlyAPICall();
        $this->myLists = [];

        foreach ($boards as $boardId => $boardName) {
            $this->gentlyAPICall();

            $url = 'https://trello.com/1/boards/'.$boardId.'/lists'
                .'?key='.env('TRELLO_KEY')
                .'&token='.env('TRELLO_TOKEN');

            if (false === ($result = $this->getter->get($url))) {
                continue;
            }

            $lists = $this->converter->convert($result);

            foreach ($lists as $list) {
                $this->myLists[$list['id']] = [
                    'name'      => $list['name'],
                    'boardId'   => $boardId,
                    'boardName' => $boardName, ];
            }
        }

        return $this->myLists;
    }

    private function getAllCards()
    {
        $lists = $this->getAllLists();

        $this->initializeGentlyAPICall();
        $this->myCards = [];

        foreach ($lists as $listId => $list) {
            $this->gentlyAPICall();

            $url = 'https://trello.com/1/lists/'.$listId.'/cards'
                .'?key='.env('TRELLO_KEY')
                .'&token='.env('TRELLO_TOKEN');

            if (false === ($result = $this->getter->get($url))) {
                continue;
            }

            $cards = $this->converter->convert($result);

            foreach ($cards as $card) {
                $this->myCards[$card['id']] = [
                    'name'      => $card['name'],
                    'listId'    => $listId,
                    'listName'  => $list['name'],
                    'boardId'   => $list['boardId'],
                    'boardName' => $list['boardName'], ];
            }
        }

        return $this->myCards;
    }

    /**
     * API制限を守る
     */
    private function initializeGentlyAPICall()
    {
        $this->benchTime = Carbon::now();
        $this->apiCall   = 0;
    }

    /**
     * API呼び出し制限の10秒当り300回を守る
     *
     * 実のところ、Trello側の制限アルゴリズムが不明なため
     * あまり意味はない。制限に引っかかるとリターンコード
     * 429が返ってくるので、それで処理したほうがベター。
     */
    private function gentlyAPICall()
    {
        if (++$this->apiCall > 300) {
            // もし301回目の呼び出しが11秒を超えている場合はWarningが発生
            // 通常は表示されないが、嫌なら今の時間と比較し回避する
            time_sleep_until($this->benchTime->addSecond(11)->timestamp);
            $this->apiCall   = 1;
            $this->benchTime = Carbon::now();
        }
    }
}

<?php

namespace App\HubConnections\Listeners;

use App\HubConnection\Exceptions\RuntimeException;
use App\HubConnections\Events\HubConnectionBaseEvent;
use App\Services\JsonArrayConverter as Converter;
use App\Services\JsonGetter;
use App\Services\JsonPoster;
use Carbon\Carbon;

/**
 * Wunderlistにある「毎日」を含むリスト上のタスクを
 * 新規タスクとしてコピーするリスナー
 */
class CopyEverydayTasks
{
    /** @var string Wundlerlistへのアクセストークン */
    private $tokens = [];

    /** inboxリストのid */
    private $inboxId = false;

    /** @var JsonGetter */
    private $getter;

    /** @var JsonPoster */
    private $poster;

    /** @var Converter */
    private $converter;

    public function __construct(JsonGetter $getter, JsonPoster $poster, Converter $converter)
    {
        $this->getter    = $getter;
        $this->poster    = $poster;
        $this->converter = $converter;
    }

    /**
     * コピー作業
     *
     * @param  HubConnectionBaseEvent  $event
     */
    public function handle(HubConnectionBaseEvent $event)
    {
        $this->tokens = [
            'X-Client-ID: '.env('WUNDERLIST_CLIENT_ID'),
            'X-Access-Token: '.env('WUNDERLIST_TOKEN'),
        ];

        // リスト情報取得
        $url       = 'a.wunderlist.com/api/v1/lists';
        $jsonLists = $this->getter->get($url, $this->tokens);
        $lists     = $this->converter->convert($jsonLists);

        // タスクコピー先を抽出（今回はinbox）
        foreach ($lists as $list) {
            if (str_contains($list['title'], 'inbox')) {
                $this->inboxId = $list['id'];
            }
        }

        if ($this->inboxId === false) {
            throw new RuntimeException('inbox list not found.');
        }

        // 「毎日」をタイトルに含むリスト抽出
        foreach ($lists as $list) {
            if (str_contains($list['title'], '毎日')) {
                // リストより新規タスク生成
                $this->createNewTasksInList($list);
            }
        }
    }

    /**
     * タスクを新規タスクとしてコピーする
     *
     * @param array $task １タスクを表す配列
     */
    private function createNewTask($task)
    {
        $newTask['list_id'] = $this->inboxId;
        $newTask['title']   = $task['title'];

        // 新規タスク生成
        $url         = 'a.wunderlist.com/api/v1/tasks';
        $jsonTask    = $this->poster->post($url, $newTask, $this->tokens);
        $createdTask = $this->converter->convert($jsonTask);

        // リマインダー設定のコピー
        $this->createRemeinderFromOriginal($task, $createdTask);
    }

    /**
     * リスト中のタスクを新規タスクとしてコピーする
     *
     * @param array $list １リストを表す配列
     */
    private function createNewTasksInList($list)
    {
        // リストのタスクを取得
        $url = 'a.wunderlist.com/api/v1/tasks'
            .'?list_id='.$list['id'];
        $jsonTasks = $this->getter->get($url, $this->tokens);
        $tasks     = $this->converter->convert($jsonTasks);

        // 各タスクをコピー
        foreach ($tasks as $task) {
            $this->createNewTask($task);
        }
    }

    /**
     * 元のタスクへリマインダー設定されていれば
     * 日付を今日に変更し、新しく設定する
     *
     * @param array $baseTask
     * @param array $newTask
     */
    private function createRemeinderFromOriginal($baseTask, $newTask)
    {
        // ベースタスクのリマインダー取得
        $url = 'a.wunderlist.com/api/v1/reminders'
            .'?task_id='.$baseTask['id'];
        $jsonReminder = $this->getter->get($url, $this->tokens);
        $reminder     = $this->converter->convert($jsonReminder);

        // リマインダー設定なし
        if (empty($reminder)) {
            return;
        }

        // リマインダーはタスクに一つしか取得できないが、
        // APIがリストのリマインダー取得と共有のため、多重配列で返ってくる
        $reminderTime = Carbon::parse($reminder[0]['date']);

        // 生成したタスクに今日の日付の同時刻でリマインダー設定
        $this->createReminder($newTask, $this->todayAtSameTime($reminderTime));
    }

    /**
     * 日本時間で「今日の同時刻」を得る
     *
     * @param Carbon $time
     * @return Carbon
     */
    private function todayAtSameTime(Carbon $time)
    {
        $timezone = $time->timezone;

        // 日本人の感覚の「今日」にするため、日本時間で設定
        $sameTime         = Carbon::now()->timezone('Asia/Tokyo');
        $time->timezone('Asia/Tokyo');
        $sameTime->hour   = $time->hour;
        $sameTime->minute = $time->minute;
        $sameTime->second = $time->second;

        return $sameTime->timezone($timezone);
    }

    /**
     * タスクにリマインダーを設定する
     *
     * @param array $task １タスクを表す配列
     * @param Carbon $date リマインダー設定日時
     */
    private function createReminder($task, Carbon $date)
    {
        $newReminder['task_id'] = $task['id'];
        $newReminder['date']    = $date->toIso8601String();

        $url          = 'a.wunderlist.com/api/v1/reminders';
        $this->poster->post($url, $newReminder, $this->tokens);
    }
}

<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\Reminder as Notifire;

/**
 * リマインダー通知コマンド
 *
 * スケジューラーにより起動され
 * メッセージをリマインダーイベントでディスパッチする。
 */
class Reminder extends BaseCommand
{
    protected $signature = 'hub:reminder '
        .'{message : Reminder message}';

    protected $description = 'Notify a reminder message.';

    /** @var Notifire * */
    private $notifire;

    public function __construct(Notifire $notifire)
    {
        $this->notifire = $notifire;
        parent::__construct();
    }

    public function handle()
    {
        // 必要な場合、ここで引数やオプションのチェックを行う
        // ビジネスロジックは別の責務として分離する
        $this->notifire->run($this->argument('message'));

        // 終了コード
        return 0;
    }
}

<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\MailChecker as Notifire;

class MailCheck extends BaseCommand
{
    protected $signature = 'hub:mailcheck';

    protected $description = 'Check new posted mails.';

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
        // 新メールチェック
        if ($this->notifire->run() === false)
        {
            $this->error('Connection failed.');
            return 1; // 異常終了(Not 0)
        }

        return 0; // 正常終了
    }
}

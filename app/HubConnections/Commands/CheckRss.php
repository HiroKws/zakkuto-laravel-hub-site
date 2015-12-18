<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\FeedChecker as Notifire;

class CheckRss extends BaseCommand
{
    protected $signature = 'hub:checkrss '
        .'{url : Site RSS feed URL}';

    protected $description = 'Check new RSS feeds.';

    /** @var Notifire * */
    private $notifire;

    public function __construct(Notifire $notifire)
    {
        $this->notifire = $notifire;

        parent::__construct();
    }

    public function handle()
    {
        // URLのチェック、FILTER_VALIDATE_URLはASCIIの文字列で構成された
        // URLの妥当性のみを調べるため、非ASCII文字を含むURLでは
        // 正しく動作しないことに留意
        $url = filter_var($this->argument('url'), FILTER_VALIDATE_URL);
        if ($url === false) {
            $this->error('Wrong URL specified.');
            return 1;
        }

        // サイトモニタリング
        if ($this->notifire->run($url) === false)
        {
            $this->error('Failed to get RSS feeds.');
            return 1; // 異常終了（非0）
        }

        // 終了コード
        return 0;
    }
}

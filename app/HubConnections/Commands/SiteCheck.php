<?php

namespace App\HubConnections\Commands;

use App\Console\Commands\BaseCommand;
use App\HubConnections\Notifiers\SiteMonitor as Notifire;

class SiteCheck extends BaseCommand
{
    protected $signature = 'hub:sitecheck '
        ."{url : Monitoring site's URL}'"
        .'{timeout=20 : Timeout second}';

    protected $description = 'Monitoring site status.';

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
        $this->notifire->run($url, $this->argument('timeout'));

        // 終了コード
        return 0;
    }
}

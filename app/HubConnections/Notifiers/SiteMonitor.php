<?php

namespace App\HubConnections\Notifiers;

use App\HubConnections\Events\SiteMonitoring\SiteDowned;
use App\HubConnections\Events\SiteMonitoring\SiteUpped;
use App\Services\JsonArrayConverter;
use App\Services\JsonGetter;
use App\Services\PriorDateReader as Reader;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem as File;

class SiteMonitor
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

    public function run($url, $timeout)
    {
        $now = Carbon::now();

        // 前回のデータを取得
        // totalDownは今回使用しないが、統計情報が必要になったら使える
        // 日時取得のオーバーヘッドが大きいなら、初期時の処理をクロージャーにするなど
        // 工夫が必要かもしれない（現状文字列で渡しているため、毎回評価される）
        $lastData = $this->converter->convert(
            $this->reader->read(storage_path().$this->convertUrlToFileName($url),
                '{'
                .'"startedAt": "'.$now->toDateTimeString().'",'
                .'"totalDown": "0",'
                .'"status": "down",'
                .'"lastChangedAt": "'.$now->toDateTimeString().'"'
                .'}'));

        // curlで接続
        $curl = curl_init($url);

        // オプション指定、サイトに合わせて調節
        $options = [
            // curl_execの戻り値に取得結果反映
            CURLOPT_RETURNTRANSFER => true,
            // 接続タイムアウト秒数指定
            CURLOPT_CONNECTTIMEOUT => $timeout,
        ];
        curl_setopt_array($curl, $options);

        // 呼び出し
        $result = curl_exec($curl);
        // 接続時間や転送時間などログしておくと統計情報に利用できる情報の配列
        // 今回はステータスコード以外未使用、未保存
        $info = curl_getinfo($curl);
        curl_close($curl);

        // 状態がダウンに変わった場合
        if ($lastData['status'] === 'up' && (!$result || $info['http_code'] !== 200)) {
            $lastData['status']        = 'down';
            $lastData['lastChangedAt'] = $now->toDateTimeString();

            $siteDown = new SiteDowned();

            $siteDown->url  = $url;
            $siteDown->time = $now;
            $siteDown->code = $info['http_code'];

            $this->dispatcher->fire($siteDown);
        }

        // 状態がアップに変わった場合
        if ($lastData['status'] === 'down' && $result && $info['http_code'] === 200) {
            $lastData['status'] = 'up';
            $lastData['totalDown'] += Carbon::createFromFormat('Y-m-d H:i:s',
                    $lastData['lastChangedAt'])->diffInMinutes($now);
            $lastData['lastChangedAt'] = $now->toDateTimeString();

            $siteUp = new SiteUpped();

            $siteUp->url  = $url;
            $siteUp->time = $now;

            $this->dispatcher->fire($siteUp);
        }

        // データの保存
        $this->file->put(storage_path().$this->convertUrlToFileName($url),
            json_encode($lastData));
    }

    private function convertUrlToFileName($url)
    {
        return '/web-'.strtr($url, ':/', '--').'.json';
    }
}

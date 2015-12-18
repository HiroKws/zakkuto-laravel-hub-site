<?php

namespace App\Services;

use Illuminate\Contracts\Logging\Log;

/**
 * APIへのGETリクエストアクセス
 */
class JsonGetter
{
    /** @var Log */
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function get($url)
    {
        // curlで接続
        $curl = curl_init($url);

        // オプション指定
        $options = [
            // curl_execの戻り値に取得結果反映
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($curl, $options);

        // 呼び出し
        $result = curl_exec($curl);
        $info   = curl_getinfo($curl);
        curl_close($curl);

        if ($result === false || $info['http_code'] != 200) {
            $this->log->warning('通信できませんでした。URL:'.$url
                .' Code:'.$info['http_code']);

            return false;
        }

        return $result;
    }
}

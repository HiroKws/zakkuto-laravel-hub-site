<?php

namespace App\Services;

use Illuminate\Contracts\Logging\Log;

/**
 * APIへのGETリクエストアクセス
 *
 * 英語のPosterには張り紙の意味しかないが…
 */
class JsonPoster
{
    /** @var Log */
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function post($url, $data = [])
    {
        // curlで接続
        $curl = curl_init($url);

        // オプションとデーター指定
        $options = [
            CURLOPT_HTTPHEADER     => ['Content-type: application/json', ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            // curl_execの戻り値に取得結果反映
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($curl, $options);

        // 呼び出し
        $result = curl_exec($curl);
        $info   = curl_getinfo($curl);
        curl_close($curl);

        if ($result === false || $info['http_code'] != 200) {
            $this->log->warning('POST通信できませんでした。URL:'.$url
                .' Code:'.$info['http_code']);

            return false;
        }

        return $result;
    }
}

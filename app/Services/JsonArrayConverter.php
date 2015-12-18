<?php

namespace App\Services;

use Illuminate\Contracts\Logging\Log;

/**
 * JSON文字列を配列に変換
 */
class JsonArrayConverter
{
    /** @var Log */
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * JSON文字列を配列に変換
     *
     * @param string $json 変換するJSON形式の文字列
     * @return mix エラー時null値
     */
    public function convert($json)
    {
        if (is_null($arrayData = json_decode($json, true))) {
            $this->log->warning('JSONへの変換エラー('.$this->getJsonLastError().')');
        }

        return $arrayData;
    }

    /**
     * 変換エラー時のエラー定数取得
     *
     * メッセージも取得できるが、意味が分かりにくい。
     * エラーの定数であれば、検索して詳細な情報を取得しやすい。
     *
     * @return string エラー定数
     */
    private function getJsonLastError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'JSON_ERROR_NONE';
            case JSON_ERROR_DEPTH:
                return 'JSON_ERROR_DEPTH';
            case JSON_ERROR_STATE_MISMATCH:
                return 'JSON_ERROR_STATE_MISMATCH';
            case JSON_ERROR_CTRL_CHAR:
                return 'JSON_ERROR_CTRL_CHAR';
            case JSON_ERROR_SYNTAX:
                return 'JSON_ERROR_SYNTAX';
            case JSON_ERROR_UTF8:
                return 'JSON_ERROR_UTF8';
            default:
                return '未定義エラー';
        }
    }
}

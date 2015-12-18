<?php

namespace App\Services;

// 現在の5.0から5.1ではFilesystemが２系統あり、紛らわしい。
// 今回はFileファサードの本体クラス。
// もう一つはStorageファサードの本体で、Flysystem系。
use Illuminate\Filesystem\Filesystem as File;

/**
 * 事前情報保存テキストファイルの読み込み
 */
class PriorDateReader
{
    /** @var File */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * 事前ファイル取得
     *
     * @param string $fileName ファイル名
     * @param string $initialValue ファイルが存在しない場合の初期値
     * @return string ファイルの内容
     */
    public function read($fileName, $initialValue = '')
    {
        if (!$this->file->isFile($fileName)) {
            return $initialValue;
        }

        // 前回の保存データーを取得
        return $this->file->get($fileName);
    }
}

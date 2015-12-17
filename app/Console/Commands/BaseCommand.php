<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * コマンド共通処理ベースコマンドクラス.
 */
class BaseCommand extends Command
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Symfonyコマンドコンポーネントにハードコードされている
     * エラーメッセージを日本語に変換する
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int Return code
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = parent::run($input, $output);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() == 'Not enough arguments.') {
                throw new \RuntimeException('引数が足りません。',
                    $e->getCode(), $e->getPrevious());
            } elseif ($e->getMessage() == 'Too many arguments.') {
                throw new \RuntimeException('引数が多すぎます。',
                    $e->getCode(), $e->getPrevious());
            } elseif (preg_match('/The "(.+)" option does not exist./',
                    $e->getMessage(), $matches)) {
                throw new \RuntimeException($matches[1].'オプションは存在していません。',
                    $e->getCode(), $e->getPrevious());
            } else {
                throw new \RuntimeException($e->getMessage(),
                    $e->getCode(), $e->getPrevious());
            }
        }

        return 0;
    }
}

<?php

namespace App\Services;

class CommandExecutor
{
    private $output;

    private $errorOutput;

    public function execute($command)
    {
        $this->output = '';

        $fd = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $pipes = array();

        $process = proc_open($command, $fd, $pipes);

        $this->output      = array();
        $this->errorOutput = array();

        if (is_resource($process)) {
            // 標準入力取り込み
            while (!feof($pipes[1])) {
                $this->output[] = fgets($pipes[1]);
            }
            fclose($pipes[1]);

            // 最後の余計な要素を削除
            array_pop($this->output);

            // 標準エラー取り込み
            while (!feof($pipes[2])) {
                $this->errorOutput[] = fgets($pipes[2]);
            }
            fclose($pipes[2]);

            // 最後の余計な要素を削除
            array_pop($this->errorOutput);

            $result = proc_close($process);
        } else {
            // 実行失敗時
            $this->errorOutput[] = array('実行失敗 : '.$command);
            return false;
        }

        return $result;
    }

    public function getMessage()
    {
        return implode('', array_merge($this->output, $this->errorOutput));
    }
}

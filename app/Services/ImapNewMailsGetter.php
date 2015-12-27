<?php

namespace App\Services;

use App\HubConnection\Exceptions\RuntimeException;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Server;
use Log;

/**
 * Imapによる新規メール取得
 *
 * ddeboer/imapパッケージを使用
 * 本来インターフェイスを噛ませたほうが交換性が上がる
 */
class ImapNewMailsGetter
{
    protected $host = '';

    protected $port = null;

    protected $user = '';

    protected $password = '';

    /** @ver Server */
    protected $server;

    /** @var SearchExpression */
    protected $search;

    /** @var Unseen */
    protected $unseen;

    public function __construct(SearchExpression $search, Unseen $unseen, Server $server = null)
    {
        $this->search = $search; // 自動注入
        $this->unseen = $unseen; // 自動注入
        $this->server = $server; // 自動依存注入されない
    }

    /**
     * 新メール取得
     *
     * @return array 取得メール
     * @throws RuntimeException
     */
    public function get()
    {
        // Serverのインスタンス時にホスト名が必要
        $this->server = is_null($this->server) ? new Server($this->host, $this->port) : $this->server;

        try {
            // IMAPへ接続
            $connection = $this->server->authenticate($this->user, $this->password);
            $mailbox    = $connection->getMailbox('INBOX');

            // 未読だけを取り込むためフラッグを指定
            $this->search->addCondition($this->unseen);

            // メッセージの取得
            $messages = $mailbox->getMessages($this->search);
        } catch (\Exception $e) {
            Log::error('メール取得失敗：'.$e->getMessage());
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        // メッセージオブジェクトの配列をそのまま返すのは
        // 結合が強いため通常の配列へ変換

        $messageArray = [];

        foreach ($messages as $message) {
            // ヘッダーからの情報
            $msg['subject'] = $message->getSubject();
            $msg['from']    = $message->getFrom()->getFullAddress();
            $msg['date']    = $message->getDate();

            // メッセージのBodyを取得すると、自動的に既読になります
            $msg['body'] = $message->getBodyText();

            $messageArray[] = $msg;
        }

        return $messageArray;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}

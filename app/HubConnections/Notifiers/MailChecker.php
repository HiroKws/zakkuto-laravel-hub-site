<?php

namespace App\HubConnections\Notifiers;

use App\HubConnection\Exceptions\RuntimeException;
use App\HubConnections\Events\MailPosted;
use App\Services\ImapNewMailsGetter as MailsGetter;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class MailChecker
{
    /** @var Dispatcher * */
    private $dispatcher;

    /** @var MailPosted * */
    private $event;

    /** @var MailsGetter * */
    private $mailsGetter;

    public function __construct(
        Dispatcher $dispatcher,
        MailPosted $event,
        MailsGetter $mailsGetter
    ) {
        $this->dispatcher  = $dispatcher;
        $this->event       = $event;
        $this->mailsGetter = $mailsGetter;
    }

    public function run()
    {
        // 新規メール取得
        $this->mailsGetter->setHost(env('IMAP_HOST'));
        $this->mailsGetter->setPort(env('IMAP_PORT'));
        $this->mailsGetter->setUser(env('IMAP_USER'));
        $this->mailsGetter->setPassword(env('IMAP_PASSWORD'));

        // 取得失敗ならば終了
        try {
            $mails = $this->mailsGetter->get();
        } catch (RuntimeException $e) {
            return false;
        }

        // 新しいメールごとにイベントを発行する
        foreach ($mails as $mail) {
            // 日付はDateTime型インスタンス
            $this->event->date = Carbon::instance($mail['date'])
                ->timezone('Asia/Tokyo');
            $this->event->from    = $mail['from'];
            $this->event->subject = $mail['subject'];
            $this->event->body    = $mail['body'];

            $this->dispatcher->fire($this->event);
        }

        return true;
    }
}

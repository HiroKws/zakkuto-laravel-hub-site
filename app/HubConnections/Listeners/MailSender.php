<?php

namespace App\HubConnections\Listeners;

use App\HubConnections\Events\HubConnectionBaseEvent;
use Illuminate\Contracts\Mail\Mailer;

class MailSender
{
    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * 受け取ったイベントをメールで送信する
     */
    public function handle(HubConnectionBaseEvent $event)
    {
        $this->mailer->raw($event,
            function ($m) {
            $m->to('my@example.com', '自分')
                ->subject('Hubサイトのメール通知');
        });
    }
}

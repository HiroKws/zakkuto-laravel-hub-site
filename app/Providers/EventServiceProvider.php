<?php

namespace App\Providers;

use App\HubConnections\Events\Reminder;
use App\HubConnections\Listeners\MailSender;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションのイベントリスナーのマップ
     *
     * @var array
     */
    protected $listen = [
        Reminder::class => [MailSender::class],
    ];

    /**
     * アプリケーションのその他のイベントの登録
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
    }
}

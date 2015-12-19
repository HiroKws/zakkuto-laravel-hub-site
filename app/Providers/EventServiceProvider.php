<?php

namespace App\Providers;

use App\HubConnections\Events\CardTaskKicked;
use App\HubConnections\Events\FeedPosted;
use App\HubConnections\Events\MailPosted;
use App\HubConnections\Events\Reminder;
use App\HubConnections\Listeners\CardSender;
use App\HubConnections\Listeners\CardTaskExecutor;
use App\HubConnections\Listeners\ChatSender;
use App\HubConnections\Listeners\MailSender;
use App\HubConnections\Listeners\SiteMonitoring\MonitoringUpdater;
use App\Listeners\EchoEvent;
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
        Reminder::class                                  => [ChatSender::class],
        MailPosted::class                                => [EchoEvent::class],
        'App\HubConnections\Events\Card*'                => [CardSender::class],
        FeedPosted::class                                => [EchoEvent::class],
        'App\HubConnections\Events\SiteMonitoring\Site*' => [MonitoringUpdater::class],
        CardTaskKicked::class                            => [CardTaskExecutor::class],
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

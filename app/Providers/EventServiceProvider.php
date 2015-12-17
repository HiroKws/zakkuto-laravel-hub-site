<?php

namespace App\Providers;

use App\Events\Reminder;
use App\Events\Reminder2;
use App\Listeners\EchoEvent;
use App\Listeners\EchoEvent2;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションのイベントリスナーのマップ.
     *
     * @var array
     */
    protected $listen = [
        Reminder::class.'*' => [EchoEvent::class],
#        Reminder::class  => [EchoEvent::class, EchoEvent2::class],
#        Reminder2::class => [EchoEvent2::class],
    ];

    /**
     * アプリケーションのその他のイベントの登録.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}

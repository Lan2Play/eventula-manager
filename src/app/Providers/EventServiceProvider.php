<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\ShopOrderStatusUpdated;
use App\Listeners\SendShopOrderStatusUpdatedNotification;
use App\Events\ShopOrderTrackingDetailsUpdated;
use App\Listeners\SendShopOrderTrackingDetailsUpdatedNotification;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ShopOrderStatusUpdated::class =>[
            SendShopOrderStatusUpdatedNotification::class,
        ],
        ShopOrderTrackingDetailsUpdated::class =>[
            SendShopOrderTrackingDetailsUpdatedNotification::class,
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

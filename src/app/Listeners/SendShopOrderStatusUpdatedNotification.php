<?php

namespace App\Listeners;

use App\Events\ShopOrderStatusUpdated;
use App\Mail\EventulaShopOrderStatusMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class SendShopOrderStatusUpdatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ShopOrderStatusUpdated $event): void
    {
        Mail::to($event->shopOrder->purchase->user)->queue(new EventulaShopOrderStatusMail($event->shopOrder->purchase->user, $event->shopOrder->purchase));
    }
}

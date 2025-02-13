<?php

namespace App\Listeners;

use App\Events\ShopOrderTrackingDetailsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\EventulaShopOrderTrackingDetailsMail;
use Mail;

class SendShopOrderTrackingDetailsUpdatedNotification
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
    public function handle(ShopOrderTrackingDetailsUpdated $event): void
    {
        Mail::to($event->shopOrder->purchase->user)->queue(new EventulaShopOrderTrackingDetailsMail($event->shopOrder->purchase->user, $event->shopOrder->purchase));        
    }
}

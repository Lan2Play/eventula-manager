<?php

namespace App\Http\Controllers\Adminapi;

use Mail;

use App\User;
use App\Purchase;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Mail\EventulaTicketOrderPaymentFinishedMail;
use App\Mail\EventulaShopOrderPaymentFinishedMail;

class PurchaseController extends Controller
{
    /**
     * set Purchase Success
     * @param Purchase $purchase
     * @return array
     */
    public function setSuccess(Purchase $purchase)
    {
        if ($purchase->status != "Pending")
        {
            return [
                'successful' => false,
                'reason' => 'purchase status not pending',
                'purchase' => $purchase,
            ];
        }
        if (!$purchase->setSuccess()) {
            return [
                'successful' => false,
                'reason' => 'purchase update failed',
                'purchase' => $purchase,
            ];
        }
        if (isset($purchase->user))
        {
            if ($purchase->getPurchaseContentType() == 'eventTickets')
            {
                Mail::to($purchase->user)->queue(new EventulaTicketOrderPaymentFinishedMail($purchase->user, $purchase));

            }
            if ($purchase->getPurchaseContentType() == 'shopOrder')
            {
                Mail::to($purchase->user)->queue(new EventulaShopOrderPaymentFinishedMail($purchase->user, $purchase));
            }
        }

        return [
            'successful' => true,
            'reason' => '',
            'purchase' => $purchase,
        ];
    }
}

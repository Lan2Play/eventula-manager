<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Mail;
use Helpers;
use App\User;
use App\Purchase;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Mail\EventulaTicketOrderPaymentFinishedMail;
use App\Mail\EventulaShopOrderPaymentFinishedMail;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    /**
     * Show All Purchases Index Page
     * @return View
     */
    public function index()
    {
        return view('admin.purchases.index')
            ->with('purchases', Helpers::paginate(Purchase::get()->sortByDesc('created_at'), 20));
    }

    /**
     * Show Shop Purchases Index Page
     * @return View
     */
    public function showShop()
    {
        return view('admin.purchases.index')
            ->with('purchases', Helpers::paginate(Purchase::has('order')->get()->sortByDesc('created_at'), 20));
    }

    /**
     * Show Event Purchases Index Page
     * @return View
     */
    public function showEvent()
    {
        return view('admin.purchases.index')
            ->with('purchases', Helpers::paginate(Purchase::has('participants')->get()->sortByDesc('created_at'), 20));
    }

    /**
     * Show Revoked Purchases Index Page
     * @return View
     */
    public function showRevoked()
    {
        return view('admin.purchases.index')
        ->withPurchases(
            Helpers::paginate(
                Purchase::whereHas('participants', function ($query) {
                    $query->where('revoked', '=', 1);
                })->get()->sortByDesc('created_at'),
                20
            )
        );
    }

    /**
     * Show Purchase Page
     * @param Purchase $purchase
     * @return View
     */
    public function show(Purchase $purchase)
    {
        return view('admin.purchases.show')
            ->with('purchase', $purchase);
    }

    /**
     * Set Purchase Success
     * @param Purchase $purchase
     * @return View
     */
    public function setSuccess(Purchase $purchase)
    {
        if ($purchase->status != "Pending") {
            Session::flash('alert-danger', 'Purchase is not pending!');
            return Redirect::to('/admin/purchases/' . $purchase->id);
        }

        if (!$purchase->setSuccess()) {
            Session::flash('alert-danger', 'Cannot set purchase status!');
            return Redirect::to('/admin/purchases/' . $purchase->id);
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

        Session::flash('alert-success', 'Successfully updated purchase status!');
        return Redirect::to('/admin/purchases/' . $purchase->id);
    }

    function delete(Purchase $purchase)
    {
        if (!$purchase->delete()) {
            Session::flash('alert-danger', 'Cannot delete Purchase');
            return Redirect::to('admin/purchases/' . $purchase->id);
        }
        Session::flash('alert-success', 'Purchase deleted');
        return Redirect::to('admin/purchases');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;
use Helpers;
use Session;

use App\ShopOrder;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OrdersController extends Controller
{
    /**
     * Show Order Index Page
     * @return view
     */
    public function index()
    {
        return view('admin.orders.index')->with('orders', ShopOrder::orderBy('created_at', 'desc')->paginate(10));
    }

    /**
     * Show Order Page
     * @param  Order  $order
     * @return View
     */
    public function show(ShopOrder $order)
    {
        return view('admin.orders.show')
            ->with('order', $order);
    }

    /**
    * Set Order as Processing
    * @param  Request $request
    * @param  Order  $order
    * @return View
    */
    public function setAsProcessing(Request $request, ShopOrder $order)
    {
        if (!$order->setAsProcessing($request)) {
            Session::flash('alert-danger', 'Cannot mark as Processing!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully marked as Processing!');
        return Redirect::back();
    }

    /**
    * Set Order as Shipped
    * @param  Request $request
    * @param  Order  $order
    * @return View
    */
    public function setAsShipped(Request $request, ShopOrder $order)
    {
        if ($order->purchase->status != "Success") {
            Session::flash('alert-danger', 'Cannot set as shipped because the payment is not completed!');
            return Redirect::to('admin/orders/' . $order->id);
        }

        if (!$order->setAsShipped($request)) {
            Session::flash('alert-danger', 'Cannot mark as Shipped!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully marked as Shipped!');
        return Redirect::back();
    }

    /**
    * Set Order as Complete
    * @param  Request $request
    * @param  Order  $order
    * @return View
    */
    public function setAsComplete(Request $request, ShopOrder $order)
    {
        if ($order->purchase->status != "Success") {
            Session::flash('alert-danger', 'Cannot set as shipped because the payment is not completed!');
            return Redirect::to('admin/orders/' . $order->id);
        }
        if (!$order->setAsComplete($request)) {
            Session::flash('alert-danger', 'Cannot mark as Complete!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully marked as Complete!');
        return Redirect::back();
    }

    /**
    * Set Order as Cancelled
    * @param  Request $request
    * @param  Order  $order
    * @return View
    */
    public function setAsCancelled(Request $request, ShopOrder $order)
    {
        if (!$order->setAsCancelled($request)) {
            Session::flash('alert-danger', 'Cannot Cancel order!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully Cancelled order!');
        return Redirect::back();
    }

    /**
    * Update Tracking Details
    * @param  Request $request
    * @param  Order  $order
    * @return View
    */
    public function updateTrackingDetails(Request $request, ShopOrder $order)
    {
        if (!$order->updateTrackingDetails($request)) {
            Session::flash('alert-danger', 'Cannot Update order!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully Updated order!');
        return Redirect::back();
    }
}

<?php

namespace App\Http\Controllers;

use QrCode;
use Settings;
use Colors;
use Helpers;
use Auth;
use Mail;

use App\Purchase;
use App\User;
use App\Event;
use App\EventTicket;
use App\ShopItem;
use App\ShopOrder;
use App\ShopOrderItem;
use App\EventParticipant;

use App\Mail\EventulaTicketOrderMail;
use App\Mail\EventulaTicketOrderPendingMail;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Omnipay\Omnipay as Omnipay;

class PaymentsController extends Controller
{
    protected $sandbox = false;


    /**
     * Checkout Page
     * @return View
     */
    public function showCheckout()
    {
        if (!Session::has(Settings::getOrgName() . '-basket')) {
            return Redirect::to('/');
        }
        return view('payments.checkout')
            ->with('basket', Helpers::formatBasket(Session::get(Settings::getOrgName() . '-basket')))
            ->with('activePaymentGateways', Settings::getPaymentGateways())
        ;
    }

    /**
     * Review Terms and Conditions of Purchase Page
     * @return View
     */
    public function showReview($paymentGateway)
    {
        if (!$paymentGateway = $this->checkParams($paymentGateway, $basket = Session::get(Settings::getOrgName() . '-basket'))) {
            return Redirect::back();
        }
        $nextEventFlag = true;
        if (array_key_exists('tickets', $basket)) {
            foreach ($basket['tickets'] as $ticketId => $quantity) {
                if (EventTicket::where('id', $ticketId)
                    ->first()
                    ->event
                    ->id
                    !=
                    Event::where('end', '>=', \Carbon\Carbon::now())
                    ->orderBy(\DB::raw('ABS(DATEDIFF(events.end, NOW()))'))
                    ->first()
                    ->id
                ) {
                    $nextEventFlag = false;
                }
            }
        }
        return view('payments.review')
            ->with('paymentGateway', $paymentGateway)
            ->with('basket', Helpers::formatBasket($basket))
            ->with('nextEventFlag', $nextEventFlag)
        ;
    }

    /**
     * Payment Details Page
     * @param  $paymentGateway
     * @return View
     */
    public function showDetails($paymentGateway)
    {
        if (!$paymentGateway = $this->checkParams($paymentGateway, $basket = Session::get(Settings::getOrgName() . '-basket'))) {
            return Redirect::back();
        }
        $delivery = false;
        $deliveryDetails = false;
        if (array_key_exists('shop', $basket)) {
            $delivery = true;
            if (array_key_exists('delivery', $basket)) {
                $deliveryDetails = $basket['delivery'];
            }
        }
        return view('payments.details')
            ->with('paymentGateway', $paymentGateway)
            ->with('basket', Helpers::formatBasket($basket, true))
            ->with('delivery', $delivery)
            ->with('deliveryDetails', $deliveryDetails)
        ;
    }

    /**
     * Delivery Details Page
     * @param  $paymentGateway
     * @return View
     */
    public function showDelivery($paymentGateway)
    {
        if (!$paymentGateway = $this->checkParams($paymentGateway, $basket = Session::get(Settings::getOrgName() . '-basket'))) {
            return Redirect::back();
        }
        return view('payments.delivery')
            ->with('paymentGateway', $paymentGateway)
            ->with('basket', Helpers::formatBasket($basket, true))
        ;
    }

    /**
     * Post Payment to Gateway
     * @param  Request $request
     * @return Redirect
     */
    public function post(Request $request)
    {
        if (!$paymentGateway = $this->checkParams($request->gateway, $basket = Session::get(Settings::getOrgName() . '-basket'))) {
            return Redirect::back();
        }
        if (array_key_exists('tickets', $basket)) {
            foreach ($basket['tickets'] as $ticketId => $quantity) {
                $ticket = EventTicket::where('id', $ticketId)->first();
                if ($ticket->event->capacity <= $ticket->event->EventParticipants->count()) {
                    Session::flash('alert-danger', __('payments.sold_out', ['eventname' => $ticket->event->display_name]));
                    return Redirect::back();
                }
            }
        }
        if (array_key_exists('shop', $basket)) {
            foreach ($basket['shop'] as $itemId => $quantity) {
                if (!ShopItem::hasStockByItemId($itemId)) {
                    $itemName = ShopItem::where('id', $itemId)->first()->name;
                    Session::flash('alert-danger', __('payments.basket_sold_out', ['itemname' => $itemName]));
                    return Redirect::to('/payment/checkout');
                }
            }
        }
        // If Order accepts delivery and no delivery details have been submitted redirect to delivery page
        if (array_key_exists('shop', $basket)) {
            if (
                !isset($request->shipping_first_name) &&
                !isset($request->shipping_last_name) &&
                !isset($request->shipping_address_1) &&
                !isset($request->shipping_postcode) &&
                !array_key_exists('delivery', $basket)
            ) {
                return Redirect::to('/payment/delivery/' . $paymentGateway);
            }
            if (!array_key_exists('delivery', $basket)) {
                 $rules = [
                    'delivery_type'   => 'required|in:event,shipping'
                ];
                $messages = [
                    'delivery_type.required' => __('payments.delivery_type_required'),
                    'delivery_type.in' => __('payments.delivery_type_in')
                ];
                $this->validate($request, $rules, $messages);
                // Check if the order is delivery to event or person
                if ($request->delivery_type == 'shipping') {
                    // Shipping Details
                    $rules = [
                        'shipping_first_name'   => 'required',
                        'shipping_last_name'    => 'required',
                        'shipping_address_1'    => 'required',
                        'shipping_postcode'     => 'required',
                    ];
                    $messages = [
                        'shipping_first_name.required'      =>  __('payments.shipping_first_name_required'),
                        'shipping_last_name.required'       =>  __('payments.shipping_last_name_required'),
                        'shipping_address_1.required'       =>  __('payments.shipping_address_1_required'),
                        'shipping_postcode.required'        =>  __('payments.shipping_postcode_required'),
                    ];
                    $this->validate($request, $rules, $messages);
                    $basket['delivery'] = [
                        'type'                  => 'shipping',
                        'shipping_first_name'   => $request->shipping_first_name,
                        'shipping_last_name'    => $request->shipping_last_name,
                        'shipping_address_1'    => $request->shipping_address_1,
                        'shipping_address_2'    => @$request->shipping_address_2,
                        'shipping_country'      => @$request->shipping_country,
                        'shipping_postcode'     => $request->shipping_postcode,
                        'shipping_state'        => @$request->shipping_state,
                    ];
                } else {
                    $basket['delivery'] = ['type' => 'event'];
                }
                Session::put(Settings::getOrgName() . '-basket', $basket);
                Session::save();
                Session::reflash();
            }
        }
        // If Credit Redirect Straight to details page
        if ($paymentGateway == 'credit' && !isset($request->confirm)) {
            return Redirect::to('/payment/details/' . $paymentGateway);
        }
        $offSitePaymentGateways = [
            'paypal_express',
            'onsite',
            'free'
        ];
        // Check if the card details have been submitted but allow off site payment gateways to continue
        if (
            $paymentGateway != 'credit' &&
            !in_array($paymentGateway, $offSitePaymentGateways) &&
            !isset($request->card_first_name) &&
            !isset($request->card_last_name) &&
            !isset($request->stripe_token)
        ) {
            return Redirect::to('/payment/details/' . $paymentGateway);
        }

        $processPaymentSkip = false;

        switch ($paymentGateway) {
            case 'stripe':
                // Stripe Post Params
                $rules = [
                    'card_first_name'   => 'required',
                    'card_last_name'    => 'required',
                    'stripe_token'      => 'required|filled',
                ];
                $messages = [
                    'card_first_name.required'      =>  __('payments.card_first_name_required'),
                    'card_last_name.required'       =>  __('payments.card_last_name_required'),
                    'stripe_token.required'         =>  __('payments.stripe_token_required'),
                    'stripe_token.filled'           =>  __('payments.stripe_token_filled'),
                ];
                $this->validate($request, $rules, $messages);
                $params = array(
                    'cancelUrl'     => $this->getCallbackCancelUrl($paymentGateway),
                    'returnUrl'     => $this->getCallbackReturnUrl($paymentGateway),
                    'amount'        => (float)Helpers::formatBasket($basket)->total,
                    'description'   => 'Purchase for ' . Settings::getOrgName(),
                    'currency'      => Settings::getCurrency(),
                    'paymentMethod' => $request->stripe_token,
                    'confirm'       => true,
                );
                $gateway = Omnipay::create('Stripe\PaymentIntents');
                $gateway->setApiKey(config('laravel-omnipay.gateways.stripe.credentials.secret'));
                break;
            case 'paypal_express':
                //Paypal Post Params
                $params = array(
                    'cancelUrl'     => $this->getCallbackCancelUrl($paymentGateway),
                    'returnUrl'     => $this->getCallbackReturnUrl($paymentGateway),
                    'name'          => Settings::getOrgName() . ' - Tickets Purchase',
                    'description'   => 'Purchase for ' . Settings::getOrgName(),
                    'amount'        => (float)Helpers::formatBasket($basket)->total,
                    'quantity'      => (string)count($basket),
                    'currency'      => Settings::getCurrency(),
                );
                $gateway = Omnipay::create('PayPal_Express');
                $gateway->setUsername(config('laravel-omnipay.gateways.paypal_express.credentials.username'));
                $gateway->setPassword(config('laravel-omnipay.gateways.paypal_express.credentials.password'));
                $gateway->setSignature(config('laravel-omnipay.gateways.paypal_express.credentials.signature'));
                break;
            case 'credit':
                $processPaymentSkip = true;
                $params = array();
                break;
            case 'onsite':
                $processPaymentSkip = true;
                $params = array();
                break;
            case 'free':
                $processPaymentSkip = true;
                $params = array();
                break;
        }
        Session::put('params', $params);
        Session::save();
        Session::reflash();
        if (!$processPaymentSkip) {

            if (config('app.debug')) {
                $this->sandbox = true;
            }
            $gateway->setTestMode($this->sandbox);

            // Send Payment
            try {
                $response = $gateway->purchase($params)->send();
            } catch (\Exception $e) {
                Session::flash('alert-danger', $e->getMessage());
                return Redirect::back();
            }
        }

        // Process Response
        // Credit
        if ($processPaymentSkip && $paymentGateway == 'credit') {
            if (!Auth::user()->checkCredit(-1 * abs((float)Helpers::formatBasket($basket)->total_credit))) {
                Session::flash('alert-danger', __('payments.credit_payment_unsuccessful'));
                return Redirect::to('/payment/failed');
            }
            $purchaseParams = [
                'user_id'           => Auth::id(),
                'type'              => 'Credit',
                'transaction_id'    => '',
                'token'             => '',
                'status'            => 'Success'
            ];
            $purchase = Purchase::create($purchaseParams);
            $this->processBasket($basket, $purchase->id);
            Auth::user()->editCredit(-1 * abs((float)Helpers::formatBasket($basket)->total_credit), false, 'Purchase', true, $purchase->id);
            Mail::to(Auth::user())->queue(new EventulaTicketOrderMail(Auth::user(), $purchase, Session::get(Settings::getOrgName() . '-basket') ));
            return Redirect::to('/payment/successful/' . $purchase->id);
        }

        // Free
        if ($processPaymentSkip && $paymentGateway == 'free') {
            $purchaseParams = [
                'user_id'           => Auth::id(),
                'type'              => 'free',
                'transaction_id'    => '',
                'token'             => '',
                'status'            => 'Success'
            ];
            $purchase = Purchase::create($purchaseParams);
            $this->processBasket($basket, $purchase->id);
            Mail::to(Auth::user())->queue(new EventulaTicketOrderMail(Auth::user(), $purchase, Session::get(Settings::getOrgName() . '-basket') ));
            return Redirect::to('/payment/successful/' . $purchase->id);
        }

        // Onsite
        if ($processPaymentSkip && $paymentGateway == 'onsite') {
            $purchaseParams = [
                'user_id'           => Auth::id(),
                'type'              => 'onsite',
                'transaction_id'    => '',
                'token'             => '',
                'status'            => 'Pending'
            ];
            $purchase = Purchase::create($purchaseParams);
            $this->processBasket($basket, $purchase->id);
            Mail::to(Auth::user())->queue(new EventulaTicketOrderPendingMail(Auth::user(), $purchase, Session::get(Settings::getOrgName() . '-basket') ));
            return Redirect::to('/payment/pending/' . $purchase->id);
        }

        if ($response->isSuccessful()) {
            // Payment was successful: update database
            try {
                $gateway->confirm([
                    'paymentIntentReference' => $response->getPaymentIntentReference(),
                    'returnUrl' => $this->getCallbackReturnUrl($paymentGateway),
                ])->send();
            } catch (\Exception $e) {
                Session::flash('alert-danger', $e->getMessage());
                return Redirect::back();
            }
            $responseStripe = $response->getData();

            $purchaseParams = [
                'user_id'           => Auth::id(),
                'type'              => 'Stripe',
                'transaction_id'    => $response->getTransactionReference() ?? $response->getPaymentIntentReference(),
                'token'             => $response->getPaymentIntentReference(),
                'status'            => 'Success'
            ];
            $purchase = Purchase::create($purchaseParams);
            $this->processBasket($basket, $purchase->id);
            Mail::to(Auth::user())->queue(new EventulaTicketOrderMail(Auth::user(), $purchase, Session::get(Settings::getOrgName() . '-basket') ));
            return Redirect::to('/payment/successful/' . $purchase->id);
        } else if($response->isRedirect()) {
            // Payment Requires redirect
            try {
                $response->redirect();
            } catch (\Exception $e) {
                Session::flash('alert-danger', $e->getMessage());
                return Redirect::back();
            }
        }

        //Failed transaction
        $message = '';
        if (!$processPaymentSkip) {
            $message = $response->getMessage();
        }
        Session::flash('alert-danger', __('payments.payment_unsuccessful') . $message);
        return Redirect::to('/payment/failed');
    }

    /**
     * Process Callback Payment
     * @param  Request $request
     * @return Redirect
     */
    public function process(Request $request)
    {
        if (!$paymentGateway = $this->checkParams($request->gate, $basket = Session::get(Settings::getOrgName() . '-basket'))) {
            return Redirect::back();
        }
        if ($request->input('type') == 'cancel') {
            Session::flash('alert-danger', __('payments.payment_cancelled'));
            return Redirect::to('/payment/cancelled');
        }
        if (!Session::has('params')) {
            Session::flash('alert-danger', __('payments.payment_unsuccessful_basic'));
            return Redirect::to('/payment/failed');
        }
        $params = Session::get('params');
        if (config('app.debug')) {
            $this->sandbox = true;
        }
        $successful = false;
        switch ($paymentGateway) {
            case 'stripe':
                $gateway = Omnipay::create('Stripe\PaymentIntents');
                $gateway->setApiKey(config('laravel-omnipay.gateways.stripe.credentials.secret'));

                //Complete Purchase
                $response = $gateway->confirm([
                    'paymentIntentReference' => $request->get('payment_intent'),
                    'returnUrl'     => $this->getCallbackReturnUrl($paymentGateway),
                ])->send();

                if ($response->isSuccessful()) {
                    //Add Purchase to database
                    $purchaseParams = [
                        'user_id'           => Auth::id(),
                        'type'              => 'Stripe',
                        'transaction_id'    => $response->getTransactionReference() ?? $response->getPaymentIntentReference(),
                        'token'             => $response->getPaymentIntentReference(),
                        'status'            => 'Success'
                    ];
                    $successful = true;
                }
                break;
            case 'paypal_express':
                $gateway = Omnipay::create('PayPal_Express');
                $gateway->setUsername(config('laravel-omnipay.gateways.paypal_express.credentials.username'));
                $gateway->setPassword(config('laravel-omnipay.gateways.paypal_express.credentials.password'));
                $gateway->setSignature(config('laravel-omnipay.gateways.paypal_express.credentials.signature'));
                $gateway->setTestMode($this->sandbox);
                //Complete Purchase
                $gateway->completePurchase($params)->send();
                $response = $gateway->fetchCheckout($params)->send(); // this is the raw response object
                $paypalResponse = $response->getData();
                if (isset($paypalResponse['ACK']) &&
                    $paypalResponse['ACK'] === 'Success' &&
                    isset($paypalResponse['PAYMENTREQUEST_0_TRANSACTIONID'])
                ) {
                    //Add Purchase to database
                    $purchaseParams = [
                        'user_id'           => Auth::id(),
                        'type'              => 'PayPal Express',
                        'transaction_id'    => $paypalResponse['PAYMENTREQUEST_0_TRANSACTIONID'],
                        'token'             => $paypalResponse['TOKEN'],
                        'status'            => $paypalResponse['ACK'],
                        'paypal_email'      => $paypalResponse['EMAIL'],
                    ];
                    $successful = true;
                }
                break;
        }
        if ($successful) {
            $purchase = Purchase::create($purchaseParams);
            $this->processBasket($basket, $purchase->id);
            return Redirect::to('/payment/successful/' . $purchase->id);
        }
        //Failed transaction
        Session::flash('alert-danger', __('payments.payment_unsuccessful'));
        return Redirect::to('/payment/failed');
    }

    /**
     * Successful Payment Page
     * @param  Purchase $purchase
     * @return View
     */
    public function showSuccessful(Purchase $purchase)
    {
        if (!Session::has('params')) {
            return Redirect::to('/');
        }
        $basket = Session::get(Settings::getOrgName() . '-basket');
        $type = 'tickets';
        if (array_key_exists('shop', $basket)) {
            $type = 'shop';
        }
        $basket = Helpers::formatBasket($basket);
        Session::forget('params');
        Session::forget(Settings::getOrgName() . '-basket');
        return view('payments.successful')
            ->with('type', $type)
            ->with('basket', $basket)
            ->with('purchase', $purchase)
        ;
    }

    /**
     * Pending Payment Page
     * @param  Purchase $purchase
     * @return View
     */
    public function showPending(Purchase $purchase)
    {
        if (!Session::has('params')) {
            return Redirect::to('/');
        }
        $basket = Session::get(Settings::getOrgName() . '-basket');
        $type = 'tickets';
        if (array_key_exists('shop', $basket)) {
            $type = 'shop';
        }
        $basket = Helpers::formatBasket($basket);
        Session::forget('params');
        Session::forget(Settings::getOrgName() . '-basket');
        return view('payments.pending')
            ->with('type', $type)
            ->with('basket', $basket)
            ->with('purchase', $purchase)
        ;
    }

    /**
     * Failed Payment Page
     * @param  Purchase $purchase
     * @return View
     */
    public function showFailed()
    {
        Session::forget('params');
        Session::forget(Settings::getOrgName() . '-basket');
        return view('payments.failed');
    }

    /**
     * Cancelled Payment Page
     * @param  Purchase $purchase
     * @return View
     */
    public function showCancelled()
    {
        Session::forget('params');
        Session::forget(Settings::getOrgName() . '-basket');
        return view('payments.cancelled');
    }

    /**
     * Process Basket for Successful Order
     * @param  $basket
     * @param  $purchaseId
     */
    private function processBasket($basket, $purchaseId)
    {
        if (array_key_exists('tickets', $basket)) {
            foreach ($basket['tickets'] as $ticketId => $quantity) {
                $ticket = EventTicket::where('id', $ticketId)->first();
                for ($i = 1; $i <= $quantity; $i++) {
                    //Add Participant to database
                    $participant = [
                        'user_id'       => Auth::id(),
                        'event_id'      => $ticket->event->id,
                        'ticket_id'     => $ticket->id,
                        'purchase_id'   => $purchaseId,
                    ];
                    EventParticipant::create($participant);
                }
            }
        } elseif(array_key_exists('shop', $basket)) {
            $status = 'EVENT';
            $deliverToEvent = true;
            if (array_key_exists('delivery', $basket) && $basket['delivery']['type'] == 'shipping') {
                $deliverToEvent = false;
                $status = 'PENDING';
            }
            $formattedBasket = Helpers::formatBasket($basket);
            $orderParams = [
                'total'                 => (float)$formattedBasket->total,
                'total_credit'          => $formattedBasket->total_credit,
                'purchase_id'           => $purchaseId,
                'status'                => $status,
                'shipping_first_name'   => @$basket['delivery']['shipping_first_name'],
                'shipping_last_name'    => @$basket['delivery']['shipping_last_name'],
                'shipping_address_1'    => @$basket['delivery']['shipping_address_1'],
                'shipping_address_2'    => @$basket['delivery']['shipping_address_2'],
                'shipping_country'      => @$basket['delivery']['shipping_country'],
                'shipping_postcode'     => @$basket['delivery']['shipping_postcode'],
                'shipping_state'        => @$basket['delivery']['shipping_state'],
                'deliver_to_event'      => $deliverToEvent,
            ];
            $order = ShopOrder::create($orderParams);
            foreach ($formattedBasket as $item) {
                $item->updateStock($item->quantity);
                $order->updateOrder($item);
            }
        }
    }

    /**
     * Check Params for the Order are correct
     * @param  $paymentGateway
     * @param  $basket
     */
    private function checkParams($paymentGateway, $basket)
    {
        $acceptedPaymentGateways = Settings::getPaymentGateways();
        if (in_array(strtolower($paymentGateway), $acceptedPaymentGateways) || $paymentGateway == 'credit') {
            $paymentGateway = strtolower($paymentGateway);
        } else {
            Session::flash('alert-danger', __('payments.payment_gateways_required', ['paymentGateways' => implode(" ", $acceptedPaymentGateways)]));
            return false;
        }
        if (!$basket = Session::get(Settings::getOrgName() . '-basket')) {
            Session::flash('alert-danger', __('payments.payment_no_basket'));
            return false;
        }
        if (!isset($paymentGateway)) {
            Session::flash('alert-danger', __('payments.payment_gateways_required', ['paymentGateways' => implode(" ", $acceptedPaymentGateways)]));
            return false;
        }
        if ($paymentGateway == 'credit' && !Settings::isCreditEnabled()) {
            Session::flash('alert-danger', __('payments.payment_credit_not_enabled'));
            return false;
        }
        if ($paymentGateway == 'credit' && !Helpers::formatBasket($basket)->allow_credit) {
            Session::flash('alert-danger', __('payments.payment_credit_not_allowed'));
            return false;
        }
		if($paymentGateway == 'free' && (Helpers::formatBasket($basket)->total > 0 || Helpers::formatBasket($basket)->total_credit > 0)) {
			Session::flash('alert-danger', __('payments.payment_method_not_allowed'));
			return false;
		}
        if ($paymentGateway != 'credit' && $paymentGateway != 'free' && $paymentGateway != 'onsite' && !Helpers::formatBasket($basket)->allow_payment) {
            Session::flash('alert-danger', __('payments.payment_method_not_allowed'));
            return false;
        }

        return $paymentGateway;
    }

    /**
     * Get Callback Return Url
     * @param  $paymentGateway
     * @return String
     */
    private function getCallbackReturnUrl($paymentGateway)
    {
        return $this->getRequestScheme($paymentGateway) . '://' . $_SERVER['HTTP_HOST'] . '/payment/callback?gate=' . $paymentGateway . '&type=return';
    }

    /**
     * Get Callback Cancel Url
     * @param  $paymentGateway
     * @return String
     */
    private function getCallbackCancelUrl($paymentGateway)
    {
        return $this->getRequestScheme($paymentGateway) . '://' . $_SERVER['HTTP_HOST'] . '/payment/callback?gate=' . $paymentGateway . '&type=cancel';
    }

    /**
     * Get Request Scheme
     * @param  $paymentGateway
     * @return $requestScheme
     */
    private function getRequestScheme($paymentGateway)
    {
        $requestScheme = 'http';
        if ((! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
            ) {
            $requestScheme = 'https';
        }
        return $requestScheme;
    }
}

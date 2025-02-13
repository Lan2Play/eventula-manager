<?php
namespace App\Mail;
use App\ShopOrderItem;
use Storage;
use URL;
use Helpers;
use App\User;
use App\ShopOrder;
use App\EventTicket;
use App\ShopItem;
use App\Purchase;
use App\Libraries\MustacheModelHelper;
use Spatie\MailTemplates\TemplateMailable;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;
use Spatie\MailTemplates\Models\MailTemplate;
use Illuminate\support\Collection;
use Illuminate\Support\Facades\Log;

class EventulaShopOrderTrackingDetailsMail extends TemplateMailable
{
    /** @var string */
    public const staticname = "Shop Order Tracking Details";

    /** @var string */
    public $firstname;

    /** @var string */
    public $surname;

    /** @var string */
    public $username;

    /** @var string */
    public $email;

    /** @var string */
    public $url;

    /** @var int */
    public $purchase_id;

    /** @var string */
    public $purchase_payment_method;

     /** @var array */
    public array $basket;

    /** @var float */
    public float $basket_total;

    /** @var float */
    public float $basket_total_credit;

    /** @var string */
    public string $shipping_first_name;
    /** @var string */
    public string $shipping_last_name;
    /** @var string */
    public string $shipping_address_1;
    /** @var string */
    public string $shipping_address_2;
    /** @var string */
    public string $shipping_country;
    /** @var string */
    public string $shipping_postcode;
    /** @var string */
    public string $shipping_state;

    /** @var string */
    public string $shipping_tracking;

    /** @var string */
    public string $shipping_note;

    /** @var string */
    public string $status;

     /** @var bool */
    public bool $deliver_to_event;
  



    public function __construct(User $user, Purchase $purchase)
    {
        $this->firstname = $user->firstname;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->username = $user->username_nice;
        $this->url = rtrim(URL::to('/'), "/") . "/";

        if (isset($purchase)) {
            $this->purchase_id = $purchase->id;
            $this->purchase_payment_method = $purchase->getPurchaseType();

            $this->status = $purchase->order->status;
            $this->deliver_to_event = $purchase->order->deliver_to_event;
    
            if ($purchase->order->hasShipping()) {
                $this->shipping_first_name = $purchase->order->shipping_first_name;
                $this->shipping_last_name = $purchase->order->shipping_last_name;
                $this->shipping_address_1 = $purchase->order->shipping_address_1;
                $this->shipping_address_2 = $purchase->order->shipping_address_2;
                $this->shipping_country = $purchase->order->shipping_country;
                $this->shipping_postcode = $purchase->order->shipping_postcode;
                $this->shipping_state = $purchase->order->shipping_state;
                $this->shipping_tracking = $purchase->order->shipping_tracking;
                $this->shipping_note = $purchase->order->shipping_note;

            }
        }


        if (isset($purchase->order->items)) {            
            $this->basket = array();

            Log::warning("items");
                      
            foreach ($purchase->order->items as $item) {


                if (get_class($item) == "App\ShopOrderItem")
                {
                $shitem = $item->item;
                Log::warning(json_encode($item->item));

                $shitem->quantity = $item->quantity;
                $this->basket[] = new MustacheModelHelper($shitem);
                }
            }
            Log::warning(json_encode($this->basket));
            
            $this->basket_total = $purchase->order->getTotalPrice();
            $this->basket_total_credit = $purchase->order->getTotalCreditPrice();
        }
    }
}
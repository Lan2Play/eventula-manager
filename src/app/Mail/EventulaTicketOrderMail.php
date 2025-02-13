<?php
namespace App\Mail;
use Storage; 
use URL;
use Helpers;
use App\User;
use App\Event;
use App\EventTicket;
use App\ShopItem;
use App\Purchase;
use App\EventParticipant;
use App\Libraries\MustacheModelHelper;
use Spatie\MailTemplates\TemplateMailable;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;
use Spatie\MailTemplates\Models\MailTemplate;
use Illuminate\support\Collection;

class EventulaTicketOrderMail extends TemplateMailable
{
    /** @var string */
    public const staticname = "Ticket Order";
   
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
    public array $purchase_participants;   

    /** @var array */
    public array $basket;     

    /** @var float */
    public float $basket_total;     

    /** @var float */
    public float $basket_total_credit;     

    public function __construct(User $user, Purchase $purchase, Array $basket)
    {
        $this->firstname = $user->firstname;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->username = $user->username_nice;
        $this->url = rtrim(URL::to('/'), "/") . "/";

        if (isset($purchase))
        {
            $this->purchase_id = $purchase->id;
            $this->purchase_payment_method = $purchase->getPurchaseType();
            $this->purchase_participants = array();            

            foreach($purchase->participants as $participant)
            {
                $this->purchase_participants[] = new MustacheModelHelper(EventParticipant::with('event','ticket')->where('id', $participant->id)->first());
            }
        }

        if (isset($basket))
        {
            $tempbasket = Helpers::formatBasket($basket);
            $this->basket = array(); 
            foreach($tempbasket->all() as $item)
            {
                if (get_class($item) == "App\EventTicket")
                {
                    $shitem = EventTicket::where('id', $item->id)->first();
                    $shitem->quantity = $item->quantity;
                    $this->basket[] = new MustacheModelHelper($shitem );
                }
            }

            $this->basket_total = $tempbasket->total;
            $this->basket_total_credit = $tempbasket->total_credit;
        }
    } 
}
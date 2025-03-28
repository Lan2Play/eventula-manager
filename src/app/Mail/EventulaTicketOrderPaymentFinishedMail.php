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

class EventulaTicketOrderPaymentFinishedMail extends TemplateMailable
{
    /** @var string */
    public const staticname = "Ticket Payment finished";
   
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

    public function __construct(User $user, Purchase $purchase)
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
    } 
}

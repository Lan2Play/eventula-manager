<?php
namespace App\Mail;
use URL;
use App\User;
use App\Purchase;
use App\Ticket;
use App\Libraries\MustacheModelHelper;
use Spatie\MailTemplates\TemplateMailable;

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
    public array $purchase_tickets;

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
            $this->purchase_tickets = array();

            foreach($purchase->tickets as $ticket)
            {
                $this->purchase_tickets[] = new MustacheModelHelper(Ticket::with('event','ticketType')->where('id', $ticket->id)->first());
            }
        }
    } 
}

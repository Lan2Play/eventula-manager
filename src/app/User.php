<?php

namespace App;

use DB;
use Auth;
use Settings;
use Colors;
use Helpers;
use App\CreditLog;
use App\EventTournament;

use \Carbon\Carbon as Carbon;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Debugbar;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{

    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'surname',
        'username_nice',
        'steamname',
        'username',
        'avatar',
        'steamid',
        'last_login',
        'email_verified_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            if (Settings::isCreditEnabled()) {
                if (Settings::getCreditRegistrationSite() != 0 || Settings::getCreditRegistrationSite() != null) {
                    $model->editCredit(Settings::getCreditRegistrationSite(), false, 'User Registration');
                }
            }
            return true;
        });
    }

    /*
     * Relationships
     */
    public function eventParticipants()
    {
        return $this->hasMany('App\EventParticipant');
    }
    public function matchMakingTeamplayers()
    {
        return $this->hasMany('App\MatchMakingTeamPlayer', 'user_id', 'id');
    }
    public function matchMakingTeams()
    {
        return $this->hasManyThrough('App\MatchMakingTeam', 'App\MatchMakingTeamPlayer', 'user_id', 'id', 'id', 'matchmaking_team_id');

    }
    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }
    public function creditLogs()
    {
        return $this->hasMany('App\CreditLog');
    }

    /**
     * Check if Admin
     * @return Boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    // TODO - Refactor this somehow. It's a bit hacky. - Possible mutators and accessors?
    /**
     * Set Active Event Participant for current User
     * @param $eventId
     */
    public function setActiveEventParticipant($event)
    {
        // TODO Enable signed in again depending on tournament setting
        if ($event->online_event)
        {
            $clauses = ['user_id' => $this->id];
        }
        else
        {
            $clauses = ['user_id' => $this->id, 'signed_in' => true];
        }

        $payedparticipant = EventParticipant::whereRelation('purchase', 'status', '=', 'Success')->where($clauses)->orderBy('updated_at', 'DESC')->first();
        $freeparticipant = EventParticipant::where('free', true)->where($clauses)->orderBy('updated_at', 'DESC')->first();


        if (isset($payedparticipant) && isset($freeparticipant))
        {
            if ($payedparticipant->updated_at->greaterThan($freeparticipant->updated_at))
            {
                $this->active_event_participant = $payedparticipant;
            }
            else
            {
                $this->active_event_participant = $freeparticipant;
            }
        }
        if (!isset($payedparticipant) && isset($freeparticipant))
        {
            $this->active_event_participant = $freeparticipant;
        }        
        if (isset($payedparticipant) && !isset($freeparticipant))
        {
            $this->active_event_participant = $payedparticipant;
        }

        Debugbar::addMessage("active_event_participant: " . json_encode($this->active_event_participant), 'setActiveEventParticipant');

    }

    /**
     * Get Free Tickets for current User
     * @param  $eventId
     * @return EventParticipants
     */
    public function getFreeTickets($eventId)
    {
        $clauses = ['user_id' => $this->id, 'free' => true, 'event_id' => $eventId];
        return EventParticipant::where($clauses)->get();
    }

    /**
     * Get Staff Tickets for current User
     * @param  $eventId
     * @return EventParticipants
     */
    public function getStaffTickets($eventId)
    {
        $clauses = ['user_id' => $this->id, 'staff' => true, 'event_id' => $eventId];
        return EventParticipant::where($clauses)->get();
    }

    /**
     * Get all Tickets for current user
     */
    public function getAllTickets($eventId)    {
        $clauses = ['user_id' => $this->id, 'event_id' => $eventId];
        $eventParticipants = EventParticipant::where($clauses)->get();
        return $eventParticipants;
    }

    public function getAllTicketsOfType(Event $event, EventTicket $ticket) {
        return EventParticipant::where([
            'user_id' => $this->id,
            'event_id' => $event->id,
            'ticket_id' => $ticket->id
        ])->get();
    }

    public function getAllTicketsInTicketGroup(Event $event, EventTicket $ticket) {
        if (empty($ticket->ticketGroup)) {
            return $this->getAllTicketsOfType($event, $ticket);
        }
        $ticketIds = EventTicket::where(['event_ticket_group_id' => $ticket->ticketGroup->id])->pluck('id')->toArray();

        return EventParticipant::where([
            'user_id' => $this->id,
            'event_id' => $event->id,
        ])
            ->whereIn('ticket_id', $ticketIds)
            ->get();
    }

    /**
    * User has at least one seatable ticket for event
    */
    public function hasSeatableTicket($eventId)    {
        $eventParticipants = $this->getAllTickets($eventId);

        foreach ($eventParticipants as $eventParticipant){
            
            if (($eventParticipant->ticket && $eventParticipant->ticket->seatable) ||
                ($eventParticipant->free || $eventParticipant->staff)) {
                return true;
            }
            
        }
        return false;
    }

    /**
     * Get seatable Tickets for current User
     * @param  $eventId
     * @param  boolean $obj
     * @return Array|Object
     */
    public function getTickets($eventId, $obj = false)
    {
        $clauses = ['user_id' => $this->id, 'event_id' => $eventId];
        $eventParticipants = EventParticipant::where($clauses)->get();
        $return = array();
        foreach ($eventParticipants as $eventParticipant) {
            if (($eventParticipant->ticket && $eventParticipant->ticket->seatable) ||
                ($eventParticipant->free || $eventParticipant->staff)
            ) {
                $seat = 'Not Seated';
                $seatingPlanName = "";
                if ($eventParticipant->seat) {                    
                    if ($eventParticipant->seat->seatingPlan) {
                        $seatingPlanName = $eventParticipant->seat->seatingPlan->getName();
                    }                    
                    $seat = $eventParticipant->seat->getName();
                }
                $return[$eventParticipant->id] = 'Participant ID: ' . $eventParticipant->id . $seat;
                if (!$eventParticipant->ticket && $eventParticipant->staff) {
                    $return[$eventParticipant->id] = 'Staff Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if (!$eventParticipant->ticket && $eventParticipant->free) {
                    $return[$eventParticipant->id] = 'Free Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if ($eventParticipant->ticket) {
                    $return[$eventParticipant->id] = $eventParticipant->ticket->name . ' - ' . $seatingPlanName . ' - ' . $seat;
                }
            }
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Check Credit amount for current user
     * @param  $amount
     * @return Boolean
     */
    public function checkCredit($amount)
    {
        if (($this->credit_total + $amount) < 0) {
            return false;
        }
        return true;
    }

    /**
     * Edit Credit for current User
     * @param  $amount
     * @param  Boolean $manual
     * @param  $reason
     * @param  Boolean $buy
     * @param  $purchaseId
     * @return Boolean
     */
    public function editCredit($amount, $manual = false, $reason = 'System Automated', $buy = false, $purchaseId = null)
    {
        $this->credit_total += $amount;
        $admin_id = null;
        if ($manual) {
            $admin_id = Auth::id();
            $reason = 'Manual Edit';
        }
        $action = 'ADD';
        if ($amount < 0) {
            $action = 'SUB';
        }
        if ($buy) {
            $action = 'BUY';
        }
        if ($amount != 0) {
            CreditLog::create([
                'user_id'       => $this->id,
                'action'        => $action,
                'amount'        => $amount,
                'reason'        => $reason,
                'purchase_id'   => $purchaseId,
                'admin_id'      => $admin_id
            ]);
        }
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Get Orders for Current User
     * @return ShopOrder
     */
    public function getOrders()
    {
        $return = collect();
        foreach ($this->purchases as $purchase) {
            if ($purchase->order) {
                $return->prepend($purchase->order);
            }
        }
        return $return;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    /**
     * Get Next Event for Current User
     * @return Event
     */
    public function getNextEvent()
    {
        $nextEvent = false;
        foreach ($this->eventParticipants as $eventParticipant) {
            if ($eventParticipant->event->end >=  Carbon::now()) {
                if (!isset($nextEvent) || !$nextEvent) {
                    $nextEvent = $eventParticipant->event;
                }
                if ($nextEvent->end >= $eventParticipant->event->end) {
                    $nextEvent = $eventParticipant->event;
                }
            }
        }
        return $nextEvent;
    }

}

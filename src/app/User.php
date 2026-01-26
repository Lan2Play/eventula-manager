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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

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
        'steam_avatar',
        'local_avatar',
        'steamid',
        'last_login',
        'email_verified_at',
        'locale'
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'unique_attended_event_count',
        'win_count',
        'avatar'
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
    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'user_id');
    }

    public function ownedTickets()
    {
        return $this->hasMany('App\Ticket', 'owner_id');
    }

    public function managedTickets()
    {
        return $this->hasMany('App\Ticket', 'manager_id');
    }

    public function getAllRoleTickets($paginate = false, $perPage = 5, $pageName = 'page')
    {
        $query = Ticket::where(function($query) {
            $query->where('user_id', $this->id)
                  ->orWhere('owner_id', $this->id)
                  ->orWhere('manager_id', $this->id);
        })->orderBy('created_at', 'desc');

        if ($paginate) {
            return $query->paginate($perPage, ['*'], $pageName);
        }

        return $query->get();
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
        if ($event->online_event) {
            $clauses = ['user_id' => $this->id];
        } else {
            $clauses = ['user_id' => $this->id, 'signed_in' => true];
        }

        $payedparticipant = Ticket::whereRelation('purchase', 'status', '=', Purchase::STATUS_SUCCESS)->where($clauses)->orderBy('updated_at', 'DESC')->first();
        $freeparticipant = Ticket::where('free', true)->where($clauses)->orderBy('updated_at', 'DESC')->first();


        if (isset($payedparticipant) && isset($freeparticipant)) {
            if ($payedparticipant->updated_at->greaterThan($freeparticipant->updated_at)) {
                $this->active_event_participant = $payedparticipant;
            } else {
                $this->active_event_participant = $freeparticipant;
            }
        }
        if (!isset($payedparticipant) && isset($freeparticipant)) {
            $this->active_event_participant = $freeparticipant;
        }
        if (isset($payedparticipant) && !isset($freeparticipant)) {
            $this->active_event_participant = $payedparticipant;
        }

        Debugbar::addMessage("active_event_participant: " . json_encode($this->active_event_participant), 'setActiveEventParticipant');
    }

    /**
     * Get Free Tickets for current User
     * @param  $eventId
     * @return Ticket
     */
    public function getFreeTickets($eventId)
    {
        $clauses = ['user_id' => $this->id, 'free' => true, 'event_id' => $eventId];
        return Ticket::where($clauses)->get();
    }

    /**
     * Get Staff Tickets for current User
     * @param  $eventId
     * @return Ticket
     */
    public function getStaffTickets($eventId)
    {
        $clauses = ['user_id' => $this->id, 'staff' => true, 'event_id' => $eventId];
        return Ticket::where($clauses)->get();
    }

    /**
     * Get all Tickets for current user
     * @param int $eventId
     * @param bool $includeRevoked
     * @param bool $managedTicketsOnly
     * @param bool $usedTicketsOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTickets($eventId,
                                  $includeRevoked = false,
                                  $managedTicketsOnly = false,
                                  $usedTicketsOnly = false) {

        if (!$eventId) {
            return collect();
        }

        $query = Ticket::where('event_id', $eventId);

        // Basis-Filter für Rollen
        if ($managedTicketsOnly && $usedTicketsOnly) {
            // Beide Flags - nur Tickets wo User sowohl Manager als auch User ist
            $query->where(function($q) {
                $q->where('manager_id', $this->id)
                    ->where('user_id', $this->id);
            });
        } elseif ($managedTicketsOnly) {
            // Nur gemanagte Tickets
            $query->where('manager_id', $this->id);
        } elseif ($usedTicketsOnly) {
            // Nur eigene Tickets
            $query->where('user_id', $this->id);
        } else {
            // Alle Tickets mit irgendeiner Verbindung
            $query->where(function($q) {
                $q->where('user_id', $this->id)
                    ->orWhere('manager_id', $this->id)
                    ->orWhere('owner_id', $this->id);
            });
        }

        // Revoked-Filter
        if (!$includeRevoked) {
            $query->where('revoked', 0);
        }

        return $query->get();
    }

    public function getAllTicketsOfType(Event $event, TicketType $ticket) {
        return Ticket::where([
            'user_id' => $this->id,
            'event_id' => $event->id,
            'ticket_type_id' => $ticket->id
        ])->get();
    }

    public function getAllTicketsInTicketGroup(Event $event, TicketType $ticket) {
        if (empty($ticket->ticketGroup)) {
            return $this->getAllTicketsOfType($event, $ticket);
        }
        $ticketIds = TicketType::where(['event_ticket_group_id' => $ticket->ticketGroup->id])->pluck('id')->toArray();

        return Ticket::where([
            'user_id' => $this->id,
            'event_id' => $event->id,
        ])
            ->whereIn('ticket_id', $ticketIds)
            ->get();
    }

    /**
     * User has at least one seatable ticket for event
     */
    public function hasSeatableTicket($eventId)
    {
        return Ticket::where('event_id', $eventId)
            ->where(function($q) {
                $q->where('user_id', $this->id)
                    ->orWhere('manager_id', $this->id)
                    ->orWhere('owner_id', $this->id);
            })
            ->where('revoked', 0)
            ->where(function($q) {
                $q->where('free', true)
                    ->orWhere('staff', true)
                    ->orWhereHas('ticketType', function($ticketTypeQuery) {
                        $ticketTypeQuery->where('seatable', true);
                    });
            })
            ->exists();

    }


    /**
     * User has at least one seatable ticket for event
     */
    public function hasManagedTickets($eventId)
    {
        return Ticket::where('event_id', $eventId)
            ->where(function($q) {
                $q->where('manager_id', $this->id);
            })
            ->where('revoked', 0)
            ->where(function($q) {
                $q->where('free', true)
                    ->orWhere('staff', true)
                    ->orWhereHas('ticketType', function($ticketTypeQuery) {
                        $ticketTypeQuery->where('seatable', true);
                    });
            })
            ->exists();
    }

    /**
     * Get seatable Tickets for current User
     * @param  $eventId
     * @param  boolean $obj
     * @return Array|Object
     */
    public function getTickets($eventId, $obj = false)
    {
        $clauses = ['user_id' => $this->id, 'event_id' => $eventId, 'revoked' => 0];
        $eventTickets = Ticket::where($clauses)->get();
        $return = array();
        foreach ($eventTickets as $eventTicket) {
            if (($eventTicket->ticketType && $eventTicket->ticketType->seatable) ||
                ($eventTicket->free || $eventTicket->staff)
            ) {

                $seat = 'Not Seated';
                $seatingPlanName = "";
                if ($eventTicket->seat) {
                    if ($eventTicket->seat->seatingPlan) {
                        $seatingPlanName = $eventTicket->seat->seatingPlan->getName();
                    }
                    $seat = $eventTicket->seat->getName();
                }
                $return[$eventTicket->id] = 'Participant ID: ' . $eventTicket->id . $seat;
                // TODO Discuss this as "bad code style?"
                if (!$eventTicket->ticketType && $eventTicket->staff) {
                    $return[$eventTicket->id] = 'Staff Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if (!$eventTicket->ticketType && $eventTicket->free) {
                    $return[$eventTicket->id] = 'Free Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if ($eventTicket->ticketType) {
                    $return[$eventTicket->id] = $eventTicket->ticketType->name . ' - ' . $seatingPlanName . ' - ' . $seat;
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
        foreach ($this->tickets as $ticket) {
            if ($ticket->event->end >=  Carbon::now()) {
                if (!isset($nextEvent) || !$nextEvent) {
                    $nextEvent = $ticket->event;
                }
                if ($nextEvent->end >= $ticket->event->end) {
                    $nextEvent = $ticket->event;
                }
            }
        }
        return $nextEvent;
    }

    /**
     * Get the user's unique attended event count.
     */
    protected function uniqueAttendedEventCount(): Attribute
    {
        $attribute =  Attribute::make(
            get: fn() => $this->tickets()
                ->whereHas('event', function (Builder $query) {
                    $query->whereIn('status', ['PUBLISHED', 'REGISTEREDONLY'])
                        ->where('end', '<=', Carbon::today());
                })
                ->select(\DB::raw('COUNT(DISTINCT event_id) as unique_attended_event_count'))
                ->value('unique_attended_event_count') ?? 0
        );

        return $attribute;
    }

    /**
     * Get the user's win count.
     */
    protected function winCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->calculateWinCount()
        );
    }

  /**
     * Get the user's avatar.
     */
    protected function avatar(): Attribute
    {
        $default_avatar = "storage/images/main/default_avatar.png";
        return Attribute::make(
            get: fn () => match ($this->selected_avatar) {
                'steam' => !empty($this->steam_avatar) ? $this->steam_avatar : asset($default_avatar),
                'local' => !empty($this->local_avatar) ? asset($this->local_avatar) : asset($default_avatar),
                default => asset($default_avatar),
            }
        );
    }

    /**
     * Calculate the user's win count.
     *
     * @return int
     */
    protected function calculateWinCount(): int
    {
        $teamWins = EventTournamentTeam::where('final_rank', 1)
            ->whereHas('tournamentParticipants.eventTicket.user', function (Builder $query) {
                $query->where('id', $this->id);
            })
            ->count();

        $individualWins = EventTournamentParticipant::where('final_rank', 1)
            ->whereHas('eventTicket.user', function (Builder $query) {
                $query->where('id', $this->id);
            })
            ->count();

        return $teamWins + $individualWins;
    }
}

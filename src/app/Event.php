<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class Event extends Model
{
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'events';

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_name',
        'status',
        'start',
        'end',
        'description',
        'seating_cap',
        'spectator_cap',
        'ticket_spectator',
        'ticket_weekend'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    protected static function boot()
    {
        parent::boot();

        $admin = false;
        if (Auth::user() && Auth::user()->getAdmin()) {
            $admin = true;
        }

        if (isset(auth('sanctum')->user()->id) && get_class(auth('sanctum')->user()) == "App\GameServer") {
            $admin = true;
        }

        if (!$admin && (Auth::user() || auth('sanctum')->user())) {
            static::addGlobalScope('statusDraft', function (Builder $builder) {
                $builder->where('status', '!=', 'DRAFT');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED')
                    ->orWhere('status', 'REGISTEREDONLY')
                    ->orWhere('status', 'PRIVATE');
            });
        }

        if (!$admin && !Auth::user() && !auth('sanctum')->user()) {
            static::addGlobalScope('statusDraft', function (Builder $builder) {
                $builder->where('status', '!=', 'DRAFT');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED')
                    ->orWhere('status', 'PRIVATE');
            });
        }
    }

    /*
     * Relationships
     */
    public function eventParticipants()
    {
        return $this->hasMany('App\EventParticipant');
    }
    public function timetables()
    {
        return $this->hasMany('App\EventTimetable');
    }
    public function ticketGroups() {
        return $this->hasMany('App\EventTicketGroup');
    }
    public function tickets()
    {
        return $this->hasMany('App\EventTicket');
    }
    public function seatingPlans()
    {
        return $this->hasMany('App\EventSeatingPlan');
    }
    public function tournaments()
    {
        return $this->hasMany('App\EventTournament');
    }
    public function sponsors()
    {
        return $this->hasMany('App\EventSponsor');
    }
    public function information()
    {
        return $this->hasMany('App\EventInformation');
    }
    public function announcements()
    {
        return $this->hasMany('App\EventAnnouncement');
    }
    public function venue()
    {
        return $this->belongsTo('App\EventVenue', 'event_venue_id');
    }
    public function galleries()
    {
        return $this->hasMany('App\GalleryAlbum');
    }
    public function polls()
    {
        return $this->hasMany('App\Poll', 'event_id');
    }
    public function tags()
    {
        return $this->hasMany('App\EventTag', 'event_id');
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nice_name'
            ]
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get Seat
     * @param  $seatingPlanId
     * @param  $seat
     * @return EventSeating
     */
    public function getSeat($seatingPlanId, $seatColumn, $seatRow)
    {
        $seatingPlan = $this->seatingPlans()->find($seatingPlanId);
        $clauses = [
            'column'    => $seatColumn,
            'row'       => $seatRow,
        ];
        return $seatingPlan->seats()->where($clauses)->first();
    }

    /**
     * Get Event Participant
     * @param  $userId
     * @return EventParticipant
     */
    public function getEventParticipant($userId = null)
    {
        if ($userId == null) {
            $userId = Auth::id();
        }
        return $this->eventParticipants()->where('user_id', $userId)->first();
    }

    /**
     * Get Total Ticket Sales
     * @return Integer
     */
    public function getTicketSalesCount()
    {
        $total = 0;
        foreach ($this->eventParticipants as $participant) {
            if ($participant->purchase && $participant->ticket) {
                $total = $total + $participant->ticket->price;
            }
        }
        return $total;
    }

    /**
     * Get Total Seated
     * @return Integer
     */
    public function getSeatedCount()
    {
        $total = 0;
        foreach ($this->seatingPlans as $seatingPlan) {
            $total += $seatingPlan->getSeatedCount();
        }
        return $total;
    }

    /**
     * Get Seating Capacity
     * @return Integer
     */
    public function getSeatingCapacity()
    {
        $total = 0;
        foreach ($this->seatingPlans as $seatingPlan) {
            $total += $seatingPlan->getSeatingCapacity();
        }
        return $total;
    }

    /**
     * Get Event Participants
     * @param  boolean $obj
     * @return Array|Boolean
     */
    public function getParticipants($obj = false)
    {
        $return = array();
        foreach ($this->eventParticipants as $participant) {
            if (($participant->staff || $participant->free) || @$participant->ticket->seatable) {
                $seat = 'Not Seated';
                $seatingPlanName = "";
                if (!empty($participant->seat)) {
                    if ($participant->seat->seatingPlan) {
                        $seatingPlanName = $participant->seat->seatingPlan->getName();
                    }
                    $seat = $participant->seat->getName();
                }                
                
                if(!empty($participant->ticket->name)) {
                    $text = $participant->user->username . ' - ' . $participant->ticket->name . ' - ' . $seatingPlanName . ' - ' . $seat;
                } else {
                    $text = $participant->user->username . ' - ' . $seatingPlanName . ' - ' . $seat;
                }

                if ($participant->staff) {
                    $text = $participant->user->username . ' - ' . 'Staff Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if ($participant->free) {
                    $text = $participant->user->username . ' - ' . 'Free Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                $return[$participant->id] = $text;
            }
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Get Timetable Data Count
     * @return Integer
     */
    public function getTimetableDataCount()
    {
        $total = 0;
        foreach ($this->timetables as $timetable) {
            $total += $timetable->data()->count();
        }
        return $total;
    }

    /**
     * Get Cheapest Ticket
     * @return Object
     */
    public function getCheapestTicket()
    {
        return $this->tickets->where('price', '!==', null)->min('price');
    }


    /**
     * Add Tags for Eventula
     * @param  Array $tags
     * @return Boolean
     */
    public function addTagsById($tags)
    {
        foreach ($this->tags as $tag) {
            $tag->delete();
        }
        foreach ($tags as $tag) {
            if (!$this->addTagById($tag)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add Tag for Eventula
     * @param  String $tags
     * @return Boolean
     */
    public function addTagById($tag)
    {
        if (!EventTag::create(['event_id' => $this->id, 'tag_id' => $tag])) {
            return false;
        }
        return true;
    }

    public function getUngroupedTickets() {
        return $this->tickets->where('event_ticket_group_id', '===', null);
    }
}

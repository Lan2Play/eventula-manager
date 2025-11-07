<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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
        'ticket_weekend',
        'private_participants',
        'matchmaking_enabled',
        'tournaments_freebies',
        'tournaments_staff',
        'tickettype_hide_policy'
    ];

    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_PRIVATE = 'PRIVATE';
    public const STATUS_REGISTEREDONLY = 'REGISTEREDONLY';


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
        // Remember There are is also an ApiGlobalScopesMiddleware used here

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


    /**
     * Scope a query to get the next upcoming event(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNextUpcoming(Builder $query, int $limit = 1): Builder
    {
        return $query->where('end', '>=', Carbon::now())
            ->orderByRaw('ABS(DATEDIFF(events.end, NOW()))')
            ->limit($limit);
    }

    /**
     * Scope a query to get the current active event(s).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Carbon\Carbon|null $now
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent(Builder $query, Carbon $now = null): Builder
    {
        $now = $now ?? Carbon::now();
        return $query->where('start', '<', $now)
            ->where('end', '>', $now)
            ->orderBy('id', 'desc');
    }

    /*
     * Relationships
     * formerly eventPaticipants
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket')->where('revoked', '=', 0);
    }
    public function allEventTickets()
    {
        return $this->hasMany('App\Ticket');
    }
    public function timetables()
    {
        return $this->hasMany('App\EventTimetable');
    }
    public function ticketGroups() {
        return $this->hasMany('App\TicketGroup');
    }
    public function ticketTypes()
    {
        return $this->hasMany('App\TicketType');
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
     * Use cached lookup for implicit route-model binding.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            if ($field === 'slug' || ($field === null && $this->getRouteKeyName() === 'slug')) {
                return self::findBySlugCached((string)$value);
            }
            if ($field === 'id' || ($field === null && $this->getRouteKeyName() === 'id')) {
                return self::findCached((int)$value);
            }
        } catch (\Throwable $e) {
            \Log::warning('Event route binding cache error: ' . $e->getMessage());
        }
        return parent::resolveRouteBinding($value, $field);
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
     * @return Ticket
     */
    public function getTicket($userId = null)
    {
        if ($userId == null) {
            $userId = Auth::id();
        }
        return $this->tickets()->where('user_id', $userId)->first();
    }

    /**
     * @return int number of unique Users with a ticket that was not revoked
     */
    public function getUniqueUsersWithTickets() {
        return $this->tickets()->where('revoked', '=', 0)->distinct()->count();
    }

    /**
     * Get Total Ticket Sales
     * @return int
     */
        public function getTicketSalesCount()
        {
            return $this->tickets()
                ->whereNotNull('purchase_id')
                ->whereHas('ticketType')
                ->join('ticket_types', 'tickets.ticket_type_id', '=', 'ticket_types.id')
                ->sum('ticket_types.price');
        }

    /**
     * Get Total Seated
     * @return int
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
     * @return int
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
        foreach ($this->tickets as $ticket) {
            if (($ticket->staff || $ticket->free) || @$ticket->ticketType->seatable) {
                $seat = 'Not Seated';
                $seatingPlanName = "";
                if (!empty($ticket->seat)) {
                    if ($ticket->seat->seatingPlan) {
                        $seatingPlanName = $ticket->seat->seatingPlan->getName();
                    }
                    $seat = $ticket->seat->getName();
                }                
                
                if(!empty($ticket->ticketType->name)) {
                    $text = $ticket->user->username . ' - ' . $ticket->ticketType->name . ' - ' . $seatingPlanName . ' - ' . $seat;
                } else {
                    $text = $ticket->user->username . ' - ' . $seatingPlanName . ' - ' . $seat;
                }

                if ($ticket->staff) {
                    $text = $ticket->user->username . ' - ' . 'Staff Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                if ($ticket->free) {
                    $text = $ticket->user->username . ' - ' . 'Free Ticket - ' . $seatingPlanName . ' - ' . $seat;
                }
                $return[$ticket->id] = $text;
            }
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Get Timetable Data Count
     * @return int
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

    /**
     * Get ungrouped tickets of Eventvent
     * @return \Illuminate\Database\Eloquent\Collection|\App\TicketType[]
     */
    public function getUngroupedTickets() {
        return $this->ticketTypes()->ungrouped()->get();;
    }

    /**
     * Get if Event is currently running
     * @return Boolean
     */
    public function isRunningCurrently()
    {
        return $this->isRunningOn(Carbon::now());
    }

    /**
     * Get if Event is running on a specific date
     * @param  Carbon $date
     * @return Boolean
     */
    public function isRunningOn(Carbon $date)
    {
        return $date->between(Carbon::parse($this->start),Carbon::parse($this->end));
    }

    // TODO: hasEnded() is not used anywhere, should it be removed?
    /**
     * Get if Event has already ended
     * @return Boolean
     */
    public function hasEnded()
    {
        return Carbon::parse($this->end)->lessThan(Carbon::now());
    }

    protected static function booted()
    {
        $invalidate = function (self $model) {
            try {
                $id = $model->id ?? null;
                $slug = $model->slug ?? null;
                if (Cache::supportsTags()) {
                    if ($id) {
                        Cache::tags(['events'])->forget("event:{$id}");
                    }
                    if ($slug) {
                        Cache::tags(['events'])->forget("event:slug:{$slug}");
                    }
                } else {
                    if ($id) {
                        Cache::forget("event:{$id}");
                    }
                    if ($slug) {
                        Cache::forget("event:slug:{$slug}");
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Cache invalidation for events failed: ' . $e->getMessage());
            }
        };

        static::saved($invalidate);
        static::deleted($invalidate);
    }

    /**
     * Find an Event by id with caching.
     * Caches the whole model instance under key event:{id}.
     */
    public static function findCached(int $id): ?self
    {
        $key = "event:{$id}";
        try {
            $remember = function () use ($id) {
                return self::query()->find($id);
            };
            if (Cache::supportsTags()) {
                return Cache::tags(['events'])->rememberForever($key, $remember);
            }
            return Cache::rememberForever($key, $remember);
        } catch (\Throwable $e) {
            \Log::warning('Cache error while loading event ' . $id . ': ' . $e->getMessage());
            return self::query()->find($id);
        }
    }

    /**
     * Find an Event by slug with caching.
     * Key: event:slug:{slug}
     */
    public static function findBySlugCached(string $slug): ?self
    {
        $key = "event:slug:{$slug}";
        try {
            $remember = function () use ($slug) {
                return self::query()->where('slug', $slug)->first();
            };
            if (Cache::supportsTags()) {
                return Cache::tags(['events'])->rememberForever($key, $remember);
            }
            return Cache::rememberForever($key, $remember);
        } catch (\Throwable $e) {
            \Log::warning('Cache error while loading event by slug ' . $slug . ': ' . $e->getMessage());
            return self::query()->where('slug', $slug)->first();
        }
    }

    /**
     * Fetch multiple events by ids efficiently with caching.
     * Returns an associative array id => Event|null (null if not found; nulls are not cached).
     *
     * @param int[] $ids
     * @return array<int, self|null>
     */
    public static function findManyCached(array $ids): array
    {
        try {
            $ids = array_values(array_unique(array_map('intval', array_filter($ids, function ($v) { return (int)$v > 0; }))));
            if (!$ids) {
                return [];
            }
            $result = [];
            $missing = [];

            if (Cache::supportsTags()) {
                foreach ($ids as $id) {
                    $cached = Cache::tags(['events'])->get("event:{$id}");
                    if ($cached === null) {
                        $missing[] = $id;
                    } else {
                        $result[$id] = $cached;
                    }
                }
            } else {
                foreach ($ids as $id) {
                    $cached = Cache::get("event:{$id}");
                    if ($cached === null) {
                        $missing[] = $id;
                    } else {
                        $result[$id] = $cached;
                    }
                }
            }

            if ($missing) {
                $fromDb = self::query()->whereIn('id', $missing)->get()->keyBy('id');
                foreach ($missing as $id) {
                    if (isset($fromDb[$id])) {
                        $model = $fromDb[$id];
                        if (Cache::supportsTags()) {
                            Cache::tags(['events'])->forever("event:{$id}", $model);
                        } else {
                            Cache::forever("event:{$id}", $model);
                        }
                        $result[$id] = $model;
                    } else {
                        $result[$id] = null; // do not cache nulls
                    }
                }
            }

            return $result;
        } catch (\Throwable $e) {
            \Log::warning('Cache error while loading multiple events: ' . $e->getMessage());
            $coll = self::query()->whereIn('id', $ids)->get()->keyBy('id');
            $out = [];
            foreach ($ids as $id) {
                $out[$id] = $coll->get($id, null);
            }
            return $out;
        }
    }

}

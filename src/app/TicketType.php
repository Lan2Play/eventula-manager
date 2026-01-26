<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TicketType extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'ticket_types';
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /*
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'ticket_type_id');
    }
    public function creditLogs()
    {
        return $this->belongsTo('App\CreditLog');
    }

    public function ticketGroup() {
        return $this->belongsTo('App\TicketGroup', 'event_ticket_group_id');
    }

    public function hasTicketGroup():bool {
        return !empty($this->ticketGroup);
    }

    public function isUpcoming():bool
    {
        if (!$this->sale_start) {
            return false;
        }
        return $this->sale_start > now();
    }

    public function isExpired():bool
    {
        if (!$this->sale_end) {
            return false;
        }
        return $this->sale_end < now();
    }

    public function isTimeless():bool {
        return (!$this->sale_start || !$this->sale_end);
    }
    public function isSoldOut(): bool
    {
        if ($this->quantity == 0)
            return false;
        return $this->tickets()->where('revoked', 0)->count() >= $this->quantity;
    }

    // TODO: find a better way to do this
    public function isHiddenByPolicy(int $policy):bool {
        debug($this->name, $policy);
        return match ($policy) {
            0 => false,
            1 => $this->isUpcoming(),
            2 => $this->isExpired(),
            3 => $this->isUpcoming() || $this->isExpired(),
            4 => $this->isSoldOut(),
            5 => $this->isUpcoming() || $this->isSoldOut(),
            6 => $this->isExpired() || $this->isSoldOut(),
            7 => $this->isUpcoming() || $this->isExpired() || $this->isSoldOut(),
            8 => $this->isTimeless(),
            9 => $this->isTimeless() || $this->isUpcoming(),
            10 => $this->isTimeless() || $this->isExpired(),
            11 => $this->isTimeless() || $this->isUpcoming() || $this->isExpired(),
            12 => $this->isTimeless() || $this->isSoldOut(),
            13 => $this->isTimeless() || $this->isUpcoming() || $this->isSoldOut(),
            14 => $this->isTimeless() || $this->isExpired() || $this->isSoldOut(),
            15 => $this->isTimeless() || $this->isUpcoming() || $this->isExpired() || $this->isSoldOut(),
            default => true,
        };
    }
    /**
     * Scope a query to only include ungrouped tickets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUngrouped(Builder $query): Builder
    {
        return $query->whereNull('event_ticket_group_id');
    }

}

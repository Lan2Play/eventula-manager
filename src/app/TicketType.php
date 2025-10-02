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

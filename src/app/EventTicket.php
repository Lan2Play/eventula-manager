<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_tickets';
    
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
    public function participants()
    {
        return $this->hasMany('App\EventParticipant', 'ticket_id');
    }
    public function creditLogs()
    {
        return $this->belongsTo('App\CreditLog');
    }

    public function ticketGroup() {
        return $this->belongsTo('App\EventTicketGroup', 'event_ticket_group_id');
    }

    public function hasTicketGroup():bool {
        return !empty($this->ticketGroup);
    }
}

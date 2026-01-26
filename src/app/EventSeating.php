<?php

namespace App;

use DB;
use Helpers;

use Illuminate\Database\Eloquent\Model;

class EventSeating extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_seating';
    
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
    public function seatingPlan()
    {
        return $this->belongsTo('App\EventSeatingPlan', 'event_seating_plan_id');
    }
    public function eventTicket()
    {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }

    /**
     * // TODO wtf the naming?
     * Get Staff Tickets for current User
     * @return string
     */
    public function getName()
    {
        
        if($this->row && $this->column){
            return Helpers::getLatinAlphabetUpperLetterByIndex($this->row) . $this->column;
        }
        
        return "";

    }

    
}

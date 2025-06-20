<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketGroup extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'ticket_groups';

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
        return $this->hasMany('App\TicketType');
    }
}

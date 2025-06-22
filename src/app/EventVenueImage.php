<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventVenueImage extends Model
{
    use HasFactory;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_venue_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'description',
        'venue_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    
    /*
     * Relationships
     */
    public function venue()
    {
        return $this->belongsTo('App\EventVenue');
    }
}

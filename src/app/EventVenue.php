<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Cviebrock\EloquentSluggable\Sluggable;

class EventVenue extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_venues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_name',
        'address_1',
        'address_2',
        'address_2',
        'address_street',
        'address_city',
        'address_postcode',
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
    public function events()
    {
        return $this->hasMany('App\Event');
    }
    public function images()
    {
        return $this->hasMany('App\EventVenueImage');
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
                'source' => 'display_name'
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
}

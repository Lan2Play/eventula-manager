<?php

namespace App;

use DateTime;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class EventTimetable extends Model
{
    use HasFactory;
    use Sluggable;

    protected $table = 'event_timetables';

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
        if (!$admin) {
            static::addGlobalScope('statusDraft', function (Builder $builder) {
                $builder->where('status', '!=', 'DRAFT');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED');
            });
        }
    }
    
    /*
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    public function data()
    {
        return $this->hasMany('App\EventTimetableData');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
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
     * Get Available Time slots for timetable
     * @param  boolean $obj
     * @return Array
     */
    public function getAvailableTimes($obj = false)
    {
        $return = array();
        $endDate = new \DateTime($this->event->end);
        $startDate = new \DateTime($this->event->start);
        while ($startDate <= $endDate) {
            $return[$startDate->format('Y-m-d H:i:s')] = date(
                "D",
                strtotime($startDate->format('Y-m-d H:i:s'))
            ) . ' - ' .  date("H:i", strtotime($startDate->format('Y-m-d H:i:s')));
            $startDate->modify('+30 minutes');
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }
}

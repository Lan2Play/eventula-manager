<?php

namespace App;

use App\EventTimetable;
use App\Http\Requests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;


class EventTimetableData extends Model
{
    use HasFactory;
    protected $table = 'event_timetable_data';

    protected $hidden = array(
        'created_at',
        'updated_at'
    );
    
    /*
     * Relationships
     */
    public function timetable()
    {
        return $this->belongsTo('App\EventTimetable');
    }


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('start_time', function (Builder $builder) {
            $builder->orderBy('start_time', 'asc');
        });
    }
}

<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class EventInformation extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_information';
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
    }
    
    /*
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
}

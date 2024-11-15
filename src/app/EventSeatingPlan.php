<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class EventSeatingPlan extends Model
{
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_seating_plans';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
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
    public function seats()
    {
        return $this->hasMany('App\EventSeating');
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
     * Get the Full Name for Lists.
     *
     * @return string
     */
    public function getName()
    {
        $name = "";
        if ($this->name !== null) {
            $name = $this->name;
        }
        return $name;
    }

    /**
     * Get the Short Name for Lists.
     *
     * @return string
     */
    public function getShortName()
    {
        $name = $this->getName();
        if ($this->name_short !== null) {
            $name = $this->name_short;
        }
        return $name;
    }

    /**
     * Get Total Seated
     * @return int
     */
    public function getSeatedCount()
    {
        return $this->seats()->where('status', 'ACTIVE')->count();;
    }

    /**
     * Get Seating Capacity
     * @return int
     */
    public function getSeatingCapacity()
    {
        return ($this->columns * $this->rows) - $this->seats()->where('status', 'INACTIVE')->count();
    }
}

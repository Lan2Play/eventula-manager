<?php

namespace App;

use DB;
use Auth;
use App\PollOption;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class Poll extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     * @var string
     */
    protected $table = 'polls';
    
    /**
     * The attributes excluded from the model's JSON form.
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
            static::addGlobalScope('statusPreview', function (Builder $builder) {
                $builder->where('status', '!=', 'PREVIEW');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED');
            });
        }
    }

    /*
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function options()
    {
        return $this->hasMany('App\PollOption', 'poll_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id');
    }

    /**
     * Return the sluggable configuration array for this model.
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
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Add Poll Option to the Database.
     * @return string
     */
    public function addOption($option)
    {
        if (!empty(trim($option))) {
            $pollOption = new PollOption();
            $pollOption->name = trim($option);
            $pollOption->user_id = Auth::id();
            $pollOption->poll_id = $this->id;
            if (!$pollOption->save()) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Get Total Votes
     * @return int
     */
    public function getTotalVotes()
    {
        $total = 0;
        foreach ($this->options as $option) {
            $total += $option->getTotalVotes();
        }
        return $total;
    }

    /**
     * End the Poll
     * @return boolean
     */
    public function endPoll()
    {
        $this->end = date("Y-m-d H:i:s");
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * See if the Poll has ended
     * @return boolean
     */
    public function hasEnded()
    {
        if ($this->end == null || ($this->end == "0000-00-00 00:00:00" || $this->end >= date("Y-m-d H:i:s"))) {
            return false;
        }
        return true;
    }

    /**
     * See if the Poll has started
     * @return boolean
     */
    public function hasStarted()
    {
        if ($this->start != "0000-00-00 00:00:00" || $this->start <= date("Y-m-d H:i:s")) {
            return false;
        }
        return true;
    }

    public function sortOptions()
    {
        $this->options = $this->options->sortBy(function ($option, $key) {
            return $option->getTotalVotes();
        });
    }
}

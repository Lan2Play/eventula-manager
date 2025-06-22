<?php

namespace App;

use App\NewsComment;
use App\NewsTag;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;

class NewsArticle extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'news_feed';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
    );

     /**
     * Scope a query to get the latest news articles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestArticles(Builder $query, int $limit = 2): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /*
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\NewsComment', 'news_feed_id');
    }

    public function tags()
    {
        return $this->hasMany('App\NewsTag', 'news_feed_id');
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
                'source' => 'title'
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
     * Store Tags
     * @param  Array $tags
     * @return Boolean
     */
    public function storeTags($tags)
    {
        $this->tags()->delete();
        $addedTags = array();
        foreach ($tags as $tag) {
            if (!in_array(trim($tag), $addedTags)) {
                $newsTag = new NewsTag();
                $newsTag->tag = trim($tag);
                $newsTag->news_feed_id = $this->id;
                if (!$newsTag->save()) {
                    return false;
                }
                array_push($addedTags, trim($tag));
            }
        }
        return true;
    }

    /**
     * Get Tags
     * @param  String $separator
     * @return Array
     */
    public function getTags($separator = ', ')
    {
        return implode($separator, $this->tags->pluck('tag')->toArray());
    }


   

}

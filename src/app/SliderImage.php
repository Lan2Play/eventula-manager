<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class SliderImage extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'slider_images';
    
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

        $invalidate = function (self $model) {
            try {
                $key = 'slider:' . ($model->slider_name ?? '');
                if (Cache::supportsTags()) {
                    Cache::tags(['slider'])->forget($key);
                } else {
                    Cache::forget($key);
                }
            } catch (\Throwable $e) {
                Log::warning('Cache invalidation for slider images failed: ' . $e->getMessage());
            }
        };

        static::saved($invalidate);
        static::deleted($invalidate);
    }

    /**
     * Get Images By Slider Name (with caching)
     * @param String $sliderName
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getImages($sliderName)
    {
        $key = "slider:{$sliderName}";
        try {
            $remember = fn() => self::where('slider_name', $sliderName)->get();
            if (Cache::supportsTags()) {
                return Cache::tags(['slider'])->rememberForever($key, $remember);
            }
            return Cache::rememberForever($key, $remember);
        } catch (\Throwable $e) {
            Log::warning('Cache error loading slider images: ' . $e->getMessage());
            return self::where('slider_name', $sliderName)->get();
        }
    }
}

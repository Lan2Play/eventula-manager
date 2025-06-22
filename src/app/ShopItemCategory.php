<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class ShopItemCategory extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'shop_item_categories';

    protected $fillable = [
        'name',
        'order',
        'status'
    ];

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
            static::addGlobalScope('statusHidden', function (Builder $builder) {
                $builder->where('status', '!=', 'HIDDEN');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED');
            });
        }
    }

    /*
     * Relationships
     */
    public function items()
    {
        return $this->hasMany('App\ShopItem', 'shop_item_category_id');
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
     * Get Total Number of Items.
     * @return string
     */
    public function getItemTotal()
    {
        return $this->items->count();
    }

    public static function getShopCategoriesSelectArray($publicOnly = true)
    {
        $return[0] = 'None';
        if ($publicOnly) {
            $categories = ShopItemCategory::where('status', 'PUBLISHED')->orderBy('name', 'ASC')->get();
        } else {
            $categories = ShopItemCategory::all()->orderBy('name', 'ASC')->get();
        }
        foreach ($categories as $category) {
            $return[$category->id] = $category->name;
        }
        return $return;
    }

}

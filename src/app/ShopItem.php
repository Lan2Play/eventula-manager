<?php

namespace App;

use DB;
use Auth;
use Settings;
use Colors;

use App\ShopOrderItem;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class ShopItem extends Model
{
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'shop_items';

    public $quantity;

    protected $fillable = [
        'name',
        'price',
        'price_credit',
        'shop_item_category_id',
        'description',
        'stock',
        'status',
        'added_by',
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
             static::addGlobalScope('caStatusPublished', function (Builder $builder) {
                $builder->whereHas('category')->where('status', 'PUBLISHED');
            });
            static::addGlobalScope('statusPublished', function (Builder $builder) {
                $builder->where('status', 'PUBLISHED');
            });
        }
        if (!Settings::isCreditEnabled()) {
            static::addGlobalScope('creditEnabled', function (Builder $builder) {
                $builder->where('price', '!=', 'null');
            });
        }
    }

    /*
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'added_by');
    }
    public function category()
    {
        return $this->belongsTo('App\ShopItemCategory', 'shop_item_category_id');
    }
    public function images()
    {
        return $this->hasMany('App\ShopItemImage', 'shop_item_id');
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
     * Get Default Image Url
     * @return int
     */
    public function getDefaultImageUrl()
    {
        if ($imagePath =
            ShopItemImage::where('shop_item_id', $this->id)
                ->where('default', true)
                ->first()
            ) {
            return $imagePath->path;
        }
        if ($imagePath =
            ShopItemImage::where('shop_item_id', $this->id)
                ->first()
            ) {
            return $imagePath->path;
        }
        return null;
    }

    /**
     * Get Total Sales
     * @return int
     */
    public function getTotalSales()
    {
        return ShopOrderItem::where('shop_item_id', $this->id)->count();
    }

    /**
     * Check if item has Stock
     * @param $itemId
     * @return Boolean
     */
    public static function hasStockByItemId($itemId)
    {
        $item = \App\ShopItem::where('id', $itemId)->first();
        if ($item->stock <= 0) {
            return false;
        }
        return true;
    }

    /**
     * Check if item has Enough Stock
     * @param $itemId
     * @return Boolean
     */
    public static function hasEnoughStockByItemId($itemId, $quantity)
    {
        $item = \App\ShopItem::where('id', $itemId)->first();
        if ($item->stock <= 0 || $quantity > $item->stock) {
            return false;
        }
        return true;
    }

    /**
     * Update Stock
     * @param $quantity
     * @param $action
     * @return Boolean
     */
    public function updateStock($quantity, $action = 'SUB')
    {
        switch ($action) {
            case 'ADD':
                $this->stock += $quantity;
                break;
            case 'SUB':
                $this->stock -= $quantity;
                break;
        }
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Add Item Image
     * @param $path
     * @return Boolean
     */
    public function addImage($path)
    {
        $image = New ShopItemImage();
        $image->path = $path;
        $image->default = false;
        $image->shop_item_id = $this->id;
        if (!$image->save()) {
            return false;
        }
        return true;
    }

    public function getPriceAttribute($value) {
        return number_format($value, 2);
    }

    public function getPriceCreditAttribute($value) {
        return number_format($value, 2);
    }

}

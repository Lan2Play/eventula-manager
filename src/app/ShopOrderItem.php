<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Cviebrock\EloquentSluggable\Sluggable;

class ShopOrderItem extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'shop_order_items';

    protected $fillable = [
        'shop_item_id',
        'quantity',
        'price',
        'price_credit',
        'shop_order_id',
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

    /*
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo('App\ShopOrder', 'shop_order_id');
    }
    public function item()
    {
        return $this->belongsTo('App\ShopItem', 'shop_item_id');
    }
}

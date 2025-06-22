<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\ShopItemCategory;
use App\ShopItem;
use App\ShopItemImage;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ## House Cleaning
        \DB::table('shop_items')->truncate();
        \DB::table('shop_item_categories')->truncate();
        \DB::table('shop_item_images')->truncate();

        ## Categories
        ShopItemCategory::factory()->count(5)->create([
        ])->each(
            function($category) {
                ShopItem::factory()->count(random_int(0, 10))->create([
                    'shop_item_category_id' => $category->id,
                ])->each(
                    function($item) {
                        ShopItemImage::factory()->create([
                            'shop_item_id'  => $item->id,
                            'default'       => 1,
                        ]);
                        ShopItemImage::factory()->count(random_int(0, 4))->create([
                            'shop_item_id' => $item->id,
                        ]);
                    }
                );
            }
        );
    }
}

<?php

namespace Database\Factories;

use App\ShopItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopItemFactory extends Factory
{
    protected $model = ShopItem::class;

    public function definition()
    {
        $name = $this->faker->words(random_int(1, 3), true);
        $rng = random_int(1, 3);
        $price = number_format(random_int(1, 100), 2);
        $price_credit = random_int(1, 999);
        
        if ($rng == 1) {
            $price = null;
        }
        if ($rng == 2) {
            $price_credit = null;
        }
        
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'featured' => random_int(0, 1),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $price,
            'price_credit' => $price_credit,
            'stock' => random_int(0, 10),
            'status' => 'PUBLISHED',
            'added_by' => 1,
        ];
    }
}

<?php

namespace Database\Factories;

use App\ShopItemCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopItemCategoryFactory extends Factory
{
    protected $model = ShopItemCategory::class;

    public function definition()
    {
        $name = $this->faker->words(random_int(1, 2), true);
        
        return [
            'name' => $name,
            'status' => 'PUBLISHED',
            'slug' => strtolower(str_replace(' ', '-', $name)),
        ];
    }
}

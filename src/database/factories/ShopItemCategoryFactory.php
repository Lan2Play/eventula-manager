<?php

namespace Database\Factories;

use App\ShopItemCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopItemCategoryFactory extends Factory
{
    protected $model = ShopItemCategory::class;

    public function definition(): array
    {
        $name = $this->faker->words(random_int(1, 2), true);
        return [
            'name'   => $name,
            'slug'   => Str::slug($name),
            'status' => 'PUBLISHED',
        ];
    }
}

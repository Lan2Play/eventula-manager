<?php

namespace Database\Factories;

use App\ShopItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopItemFactory extends Factory
{
    protected $model = ShopItem::class;

    public function definition(): array
    {
        $name  = $this->faker->words(random_int(1, 3), true);
        $rng   = random_int(1, 3);
        $price = $rng === 1 ? null : number_format(random_int(1, 100), 2);
        $priceCredit = $rng === 2 ? null : random_int(1, 999);

        return [
            'name'         => $name,
            'slug'         => Str::slug($name),
            'featured'     => (bool) random_int(0, 1),
            'description'  => $this->faker->paragraphs(2, true),
            'price'        => $price,
            'price_credit' => $priceCredit,
            'stock'        => random_int(0, 10),
            'status'       => 'PUBLISHED',
            'added_by'     => 1,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition()
    {
        return [
            // Default empty definition - specific attributes will be passed in seeders
        ];
    }
}

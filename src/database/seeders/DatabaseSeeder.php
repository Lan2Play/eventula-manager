<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AppearanceTableSeeder::class);
        $this->call(EventsSeeder::class);
        $this->call(GamesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(SliderImagesTableSeeder::class);
    }
}

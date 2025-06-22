<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Appearance;

class AppearanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ## House Cleaning
        \DB::table('appearance')->delete();

        Appearance::factory()->create([
            'key'   => 'color_primary',
            'value' => 'orange',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_primary_text',
            'value' => 'white',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_secondary',
            'value' => '#333',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_secondary_text',
            'value' => 'white',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_body_links',
            'value' => 'orange',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_body_background',
            'value' => '#fff',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_body_text',
            'value' => '#333',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_header_background',
            'value' => '#333',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_header_text',
            'value' => '#9d9d9d',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_header_links',
            'value' => '#fff',
            'type'  => 'CSS_VAR',
        ]);

        Appearance::factory()->create([
            'key'   => 'color_header_links_hover',
            'value' => 'orange',
            'type'  => 'CSS_VAR',
        ]);        
        
        Appearance::factory()->create([
            'key'   => 'color_header_line',
            'value' => 'orange',
            'type'  => 'CSS_VAR',
        ]);

    }
}

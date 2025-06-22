<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\SliderImage;

class SliderImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ## House Cleaning
        \DB::table('slider_images')->truncate();

        SliderImage::factory()->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/1.jpg',
            'order'       	=> '4',
        ]);

        SliderImage::factory()->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/2.jpg',
            'order'       	=> '1',
        ]);

        SliderImage::factory()->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/3.jpg',
            'order'       	=> '2',
        ]);

        SliderImage::factory()->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/4.jpg',
            'order'       	=> '5',
        ]);

        SliderImage::factory()->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/5.jpg',
            'order'       	=> '3',
        ]);
    }
}

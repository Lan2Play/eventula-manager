<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\SliderImage;
use Faker\Factory as Faker;

class SliderImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        ## House Cleaning
        \DB::table('slider_images')->truncate();


     	factory(SliderImage::class)->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/1.jpg',
            'order'       	=> '4',
        ]);

        factory(SliderImage::class)->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/2.jpg',
            'order'       	=> '1',
        ]);

        factory(SliderImage::class)->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/3.jpg',
            'order'       	=> '2',
        ]);

        factory(SliderImage::class)->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/4.jpg',
            'order'       	=> '5',
        ]);

        factory(SliderImage::class)->create([
            'slider_name' 	=> 'frontpage',
            'path'   		=> '/storage/images/main/slider/frontpage/5.jpg',
            'order'       	=> '3',
        ]);
    }
}

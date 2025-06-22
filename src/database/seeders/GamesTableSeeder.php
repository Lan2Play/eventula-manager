<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Game;
use Helpers;
use HaydenPierce\ClassFinder\ClassFinder;


class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ## House Cleaning
        \DB::table('games')->delete();

        Game::factory()->create([
            'name'          => 'Quake',
            'description'   => 'Best game ever',
            'version'       => 'latest',
        ]);
        
        foreach (Helpers::getGameTemplates() as $class)
        {
            $this->call($class::class);
        }

    }
}

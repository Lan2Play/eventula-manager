<?php

namespace Database\Seeders;

use App\Libraries\Helpers;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Game;
use App\MatchMaking;
use App\MatchMakingTeam;
use App\MatchMakingTeamPlayer;
use App\MatchReplay;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TestTournamentDemoSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $challongematchid = intval(Helpers::getEnvWithFallback('challongematchid', 0));
        $democount = intval(Helpers::getEnvWithFallback('democount', 0));

        if ($challongematchid == 0)
        {
            throw new \Exception("you have to provide a challongematchid");
        }
        if ($democount == 0)
        {
            throw new \Exception("you have to provide a demo count");
        }


        for ($i = 1; $i <= $democount; $i++) {

            $demoname = "demo_" . strval($challongematchid) . "_" . strval($i) . ".demo";

            $destinationPathDemo =  MatchReplay::createReplayPath(Game::where('name', 'Counter-Strike 2')->first(), $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, random_bytes(2048)) == false) {
                throw new \Exception("could not save demo file");
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->challonge_match_id = $challongematchid;
            if (!$replay->save()) {
                throw new \Exception("could not save demo");
            }
        }
    }
}

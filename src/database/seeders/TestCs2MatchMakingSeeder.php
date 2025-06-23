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

class TestCs2MatchMakingSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $playeroneid = intval(Helpers::getEnvWithFallback('playeroneid', 53));
        $playertwoid = intval(Helpers::getEnvWithFallback('playertwoid', 54));
        $democount = intval(Helpers::getEnvWithFallback('democount', 0));
        $status = Helpers::getEnvWithFallback('status', "WAITFORPLAYERS");

        if ($playeroneid == $playertwoid) {
            throw new \Exception("player ids cannot be the same");
        }

        $availablestates = ['DRAFT','OPEN','CLOSED','PENDING','WAITFORPLAYERS','LIVE','COMPLETE'];

        if (!in_array($status, $availablestates))
        {
            throw new \Exception("invalid state");
        }


        $mm = new MatchMaking(
            [
                'game_id' => Game::where('name', 'Counter-Strike 2')->first()->id,
                'team_size' => 1,
                'team_count' => 2,
                'status' => $status,
                'owner_id' => $playeroneid,
                'invite_tag' => "match_" . Str::random(),
                'ispublic' => true,
            ]
        );
        if (!$mm->save()) {
            throw new \Exception("could not save matchmaking");
        }

        $team1                             = new MatchMakingTeam();
        $team1->name                       = "team1";
        $team1->team_owner_id                 = $playeroneid;
        $team1->team_invite_tag             = "team_" . Str::random();
        $team1->match_id                    = $mm->id;
        if (!$team1->save()) {

            throw new \Exception("could not save team1");
        }

        $teamplayerone                             = new MatchMakingTeamPlayer();
        $teamplayerone->matchmaking_team_id                       = $team1->id;
        $teamplayerone->user_id                  = $playeroneid;
        if (!$teamplayerone->save()) {

            throw new \Exception("could not save teamplayerone");
        }


        $team2                             = new MatchMakingTeam();
        $team2->name                       = "team2";
        $team2->team_owner_id                 = $playertwoid;
        $team2->team_invite_tag             = "team_" . Str::random();
        $team2->match_id                    = $mm->id;
        if (!$team2->save()) {

            throw new \Exception("could not save team2");
        }

        $teamplayertwo                             = new MatchMakingTeamPlayer();
        $teamplayertwo->matchmaking_team_id                       = $team2->id;
        $teamplayertwo->user_id                  = $playertwoid;
        if (!$teamplayertwo->save()) {

            throw new \Exception("could not save teamplayertwo");
        }


        for ($i = 1; $i <= $democount; $i++) {

            $demoname = "demo_" . strval($mm->id) . "_" . strval($i) . ".demo";

            $destinationPathDemo =  MatchReplay::createReplayPath(Game::where('name', 'Counter-Strike 2')->first(), $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, random_bytes(2048)) == false) {
                throw new \Exception("could not save demo file");
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->matchmaking_id = $mm->id;
            if (!$replay->save()) {
                throw new \Exception("could not save demo");
            }
        }
    }
}

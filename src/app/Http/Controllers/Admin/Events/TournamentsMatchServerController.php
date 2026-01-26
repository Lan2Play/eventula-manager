<?php

namespace App\Http\Controllers\Admin\Events;

use Session;
use Input;

use App\Event;
use App\EventTournament;
use App\EventTournamentMatchServer;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class TournamentsMatchServerController extends Controller
{

    /**
     * Store TournamentsMatchServer to Database
     * @param  Event            $event
     * @param  EventTournament  $tournament
     * @param  int $challongeMatchId
     * @param  Request $request
     * @return Redirect
     */
    public function store(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {
        $tournamentMatchServer                 = new EventTournamentMatchServer();
        $tournamentMatchServer->challonge_match_id        = $challongeMatchId;
        $tournamentMatchServer->event_tournament_id        = $tournament->id;
        $tournamentMatchServer->game_server_id = $request->gameServer;

        if (!$tournamentMatchServer->save()) {
            Session::flash('alert-danger', 'Could not save tournamentMatchServer!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved tournamentMatchServer!');
        return Redirect::back();
    }


    /**
     * update TournamentsMatchServer to Database
     * @param  Event            $event
     * @param  EventTournament  $tournament
     * @param  int  $challongeMatchId
     * @param  Request $request
     * @return Redirect
     */
    public function update(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {

        $tournamentMatchServer = EventTournamentMatchServer::where(['challonge_match_id' => $challongeMatchId])->first();

        if (!isset($tournamentMatchServer))
        {
            Session::flash('alert-danger', 'could not find the corresponding server entry!');
            return Redirect::back();
        }

        $tournamentMatchServer->game_server_id = $request->gameServer;

        if (!$tournamentMatchServer->save()) {
            Session::flash('alert-danger', 'Could not save tournamentMatchserver!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved tournamentMatchserver!');
        return Redirect::back();

   }

    /**
     * Store TournamentsMatchServer to Database
     * @param  Event            $event
     * @param  EventTournament  $tournament
     * @param  int  $challongeMatchId
     * @param  Request $request
     * @return Redirect
     */
    public function destroy(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {
        $tournamentMatchServer = EventTournamentMatchServer::where(['challonge_match_id' => $challongeMatchId])->first();

        if (!isset($tournamentMatchServer))
        {
            Session::flash('alert-danger', 'could not find the corresponding tournamentMatchserver entry!');
            return Redirect::back();
        }

        if (!$tournamentMatchServer->delete()) {
            Session::flash('alert-danger', 'Cannot delete tournamentMatchserver!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted tournamentMatchserver!');
        return Redirect::back();
    }
}

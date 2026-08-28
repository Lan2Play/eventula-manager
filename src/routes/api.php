<?php

/**
 * API Routes
 *
 * REST API, gameserver API, and user API routes (Sanctum-guarded).
 * Migrated from app/Http/routes.php — see that file for migration notes.
 */

Route::group(['middleware' => ['installed']], function () {

    /**
     * API
     * TODO Replace path *participants* with *ticket*
     * TODO Replace path *tickets* with *tickettype*
     */
    Route::group(['middleware' => ['api', 'nodebugbar']], function () {
        Route::get('/api/events/', 'Api\Events\EventsController@index');
        Route::get('/api/events/upcoming', 'Api\Events\EventsController@showUpcoming');
        Route::get('/api/events/{event}', 'Api\Events\EventsController@show');
        Route::get('/api/events/{event}/participants', 'Api\Events\TicketController@index');
        Route::get('/api/events/{event}/timetables', 'Api\Events\TimetablesController@index');
        Route::get('/api/events/{event}/timetables/{timetable}', 'Api\Events\TimetablesController@show');
        Route::get('/api/events/{event}/announcements', 'Api\Events\AnnouncementsController@index');
        Route::get('/api/events/{event}/announcements/{announcement}', 'Api\Events\AnnouncementsController@show');
        Route::get('/api/events/{event}/tickets', 'Api\Events\TicketTypeController@index');
        Route::get('/api/events/{event}/tickets/{ticketType}', 'Api\Events\TicketTypeController@show');
        Route::get('/api/events/{event}/tournaments', 'Api\Events\TournamentsController@index');
        Route::get('/api/events/{event}/tournaments/{tournament}', 'Api\Events\TournamentsController@show');
        Route::get('/api/events/{event}/tournaments/{tournament}/challonge', 'Api\Events\TournamentsController@showChallonge');

        Route::group(['middleware' => ['auth:sanctum']], function () {
            /**
             * User API
             */
            Route::get('/api/user/me', 'Userapi\MeController@getMe');
            Route::get('/api/user/event/participants', 'Userapi\Events\TicketsController@getTickets');

            /**
             * Gameserver API
             */
            Route::group(['middleware' => ['gameserver']], function () {
                Route::post('/api/matchmaking/{match}/demo/', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchDemo');
                Route::post('/api/matchmaking/{match}/freeserver/', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchFreeServer');
                Route::post('/api/matchmaking/{match}/finalize/', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchFinalize');
                Route::post('/api/matchmaking/{match}/finalize/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchFinalizeMap');
                Route::post('/api/matchmaking/{match}/golive/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchGolive');
                Route::post('/api/matchmaking/{match}/updateround/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchUpdateround');
                Route::post('/api/matchmaking/{match}/updateplayer/{mapnumber}/{player}', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchUpdateplayer');
                Route::get('/api/matchmaking/{match}/configure/{nummaps}', 'Api\GameMatchApi\GameMatchApiController@matchMakingMatchConfig');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/demo/', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchDemo');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/freeserver/', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchFreeServer');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/finalize/', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchFinalize');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/finalize/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchFinalizeMap');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/golive/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchGolive');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/updateround/{mapnumber}', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchUpdateround');
                Route::post('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/updateplayer/{mapnumber}/{player}', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchUpdateplayer');
                Route::get('/api/events/{event}/tournaments/{tournament}/{challongeMatchId}/configure/{nummaps}', 'Api\GameMatchApi\GameMatchApiController@tournamentMatchConfig');
            });

            /**
             * Admin API
             * TODO replace path *participants* with *ticket*
             */
            Route::group(['middleware' => ['admin']], function () {
                Route::get('/api/admin/event/participants/{ticket}/signIn', 'Adminapi\Events\TicketController@signIn');
                Route::get('/api/admin/event/participants/{ticket}', 'Adminapi\Events\TicketController@getTicket');
                Route::get('/api/admin/event/participants/', 'Adminapi\Events\TicketController@getParticipants');
                Route::get('/api/admin/purchases/{purchase}/setSuccess', 'Adminapi\PurchaseController@setSuccess');
            });
        });
    });
});

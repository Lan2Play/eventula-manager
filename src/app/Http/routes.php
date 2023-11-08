<?php

/**
 * phpinfo in debug mode
 */
if (config('app.debug') === true) {
    Route::get('phpinfo', function () {
        phpinfo();
    })->name('phpinfo');
}

/**
 * Install
 */
Route::group(['middleware' => ['web', 'notInstalled']], function () {
    Route::get('/install', 'InstallController@installation');
    Route::post('/install', 'InstallController@install');
});

Route::group(['middleware' => ['installed']], function () {


    Route::group(['middleware' => 'language'], function () {



        /**
         * Image Converter
         */
        Route::get('{image}', 'Api\Images\WebpController@convert')->where('image', '.*\.webp');

        /**
         * API
         */
        Route::group(['middleware' => ['api', 'nodebugbar']], function () {
            Route::get('/api/events/', 'Api\Events\EventsController@index');
            Route::get('/api/events/upcoming', 'Api\Events\EventsController@showUpcoming');
            Route::get('/api/events/{event}', 'Api\Events\EventsController@show');
            Route::get('/api/events/{event}/participants', 'Api\Events\ParticipantsController@index');
            Route::get('/api/events/{event}/timetables', 'Api\Events\TimetablesController@index');
            Route::get('/api/events/{event}/timetables/{timetable}', 'Api\Events\TimetablesController@show');
            Route::get('/api/events/{event}/tickets', 'Api\Events\TicketsController@index');
            Route::get('/api/events/{event}/tickets/{ticket}', 'Api\Events\TicketsController@show');

            Route::group(['middleware' => ['auth:sanctum']], function () {
                /**
                 * User API
                 */
                Route::get('/api/user/me', 'Userapi\MeController@getMe');
                Route::get('/api/user/event/participants', 'Userapi\Events\ParticipantsController@getParticipants');

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
                 */
                Route::group(['middleware' => ['admin']], function () {
                    Route::get('/api/admin/event/participants/{participant}/signIn', 'Adminapi\Events\ParticipantsController@signIn');
                    Route::get('/api/admin/event/participants/{participant}', 'Adminapi\Events\ParticipantsController@getParticipant');
                    Route::get('/api/admin/event/participants/', 'Adminapi\Events\ParticipantsController@getParticipants');
                    Route::get('/api/admin/purchases/{purchase}/setSuccess', 'Adminapi\PurchaseController@setSuccess');
                });
            });
        });



        /**
         * Front End
         */


        Route::group(['middleware' => ['web']], function () {

            /**
             * Login & Register
             */
            Route::get('/register/email/verify', 'Auth\VerificationController@show')->name('verification.notice');
            Route::get('/register/email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
            Route::get('/register/email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

            Route::get('/register/{method}', 'Auth\AuthController@showRegister');
            Route::post('/register/{method}', 'Auth\AuthController@register');

            Route::get('/login', 'Auth\AuthController@prompt');

            Route::get('/login/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('/login/forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

            Route::post('/login/reset/password', 'Auth\ResetPasswordController@reset')->name('password.update');
            Route::get('/login/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

            Route::get('/login/steam', 'Auth\SteamController@login');

            Route::post('/login/standard', 'Auth\LoginController@login')->name('login.standard');;

            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/account', 'AccountController@index');
                Route::get('/account/ticket/pdf/{ticketId}', 'AccountController@pdfTicket');
                Route::get('/account/sso/remove/{method}', 'AccountController@showRemoveSso');
                Route::post('/account/sso/remove/{method}', 'AccountController@removeSso');
                Route::get('/account/sso/add/{method}', 'AccountController@addSso');
                Route::get('/account/tokens/wizzard/start/{application?}/{callbackurl?}', 'AccountController@showTokenWizzardStart');
                Route::post('/account/tokens/wizzard/finish', 'AccountController@showTokenWizzardFinish');
                Route::post('/account/tokens/add', 'AccountController@addToken');
                Route::delete('/account/tokens/remove/{token}', 'AccountController@removeToken');
                Route::post('/account', 'AccountController@update');
                Route::post('/account/delete', 'Auth\SteamController@destroy');
            });

            Route::group(['middleware' => ['auth', 'banned']], function () {
                Route::get('/account/email', 'AccountController@showMail');
                Route::post('/account/email', 'AccountController@updateMail');
            });


            Route::group(['middleware' => ['auth']], function () {
                Route::get('/logout', 'Auth\AuthController@logout');
            });

            /**
             * Index Page
             */
            Route::get('/', 'HomeController@index');

            /**
             * News Pages
             */
            Route::get('/news', 'NewsController@index');
            Route::get('/news/{newsArticle}', 'NewsController@show');
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::post('/news/{newsArticle}/comments', 'NewsController@storeComment');
                Route::post('/news/{newsArticle}/comments/{newsComment}', 'NewsController@editComment');
                Route::get('/news/{newsArticle}/comments/{newsComment}/report', 'NewsController@reportComment');
                Route::get('/news/{newsArticle}/comments/{newsComment}/delete', 'NewsController@destroyComment');
            });
            Route::get('/news/tags/{newsTag}', 'NewsController@showTag');

            /**
             * Events
             */
            Route::get('/events', 'Events\EventsController@index');
            Route::get('/events/{event}', 'Events\EventsController@show');
            Route::get('/events/{event}/big', 'HomeController@bigScreen');

            /**
             * Misc Pages
             */
            Route::get('/about', 'HomeController@about');
            Route::get('/contact', 'HomeController@contact');
            Route::get('/terms', 'HomeController@terms');
            Route::get('/legalnotice', 'HomeController@legalNotice');


            /**
             * Tickets
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/tickets/retrieve/{participant}', 'Events\TicketsController@retrieve');
                Route::post('/tickets/purchase/{ticket}', 'Events\TicketsController@purchase');
            });

            /**
             * Gifts
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/gift/accept', 'Events\ParticipantsController@acceptGift');
                Route::post('/gift/{participant}', 'Events\ParticipantsController@gift');
                Route::post('/gift/{participant}/revoke', 'Events\ParticipantsController@revokeGift');
            });

            /**
             * Galleries
             */
            Route::get('/gallery', 'GalleryController@index');
            Route::get('/gallery/{album}', 'GalleryController@show');

            /**
             * Help
             */
            Route::get('/help', 'HelpController@index');

            /**
             * Tournaments
             */
            Route::get('/events/{event}/tournaments', 'Events\TournamentsController@index');
            Route::get('/events/{event}/tournaments/{tournament}', 'Events\TournamentsController@show');
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::post('/events/{event}/tournaments/{tournament}/register', 'Events\TournamentsController@registerSingle');
                Route::post('/events/{event}/tournaments/{tournament}/register/team', 'Events\TournamentsController@registerTeam');
                Route::post('/events/{event}/tournaments/{tournament}/register/pug', 'Events\TournamentsController@registerPug');
                Route::post('/events/{event}/tournaments/{tournament}/register/remove', 'Events\TournamentsController@unregister');
            });

            /**
             * GameServers
             */
            Route::get('/games/{game}/gameservers/{gameServer}/status', 'GameServersController@status');


            /**
             * MatchMaking
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/matchmaking', 'MatchMakingController@index');
                Route::get('/matchmaking/invite', 'MatchMakingController@showInvite');
                Route::get('/matchmaking/{match}', 'MatchMakingController@show');
                Route::post('/matchmaking', 'MatchMakingController@store');
                Route::post('/matchmaking/{match}/team/{team}/teamplayer/add', 'MatchMakingController@addusertomatch');
                Route::delete('/matchmaking/{match}/team/{team}/teamplayer/{teamplayer}/delete', 'MatchMakingController@deleteuserfrommatch');
                Route::post('/matchmaking/{match}/team/{team}/teamplayer/{teamplayer}/change', 'MatchMakingController@changeuserteam');
                Route::post('/matchmaking/{match}/team/add', 'MatchMakingController@addteam');
                Route::post('/matchmaking/{match}/team/{team}/update', 'MatchMakingController@updateteam');
                Route::delete('/matchmaking/{match}/team/{team}/delete', 'MatchMakingController@deleteteam');
                Route::post('/matchmaking/{match}/update', 'MatchMakingController@update');
                Route::post('/matchmaking/{match}/start', 'MatchMakingController@start');
                Route::post('/matchmaking/{match}/open', 'MatchMakingController@open');
                Route::post('/matchmaking/{match}/scramble', 'MatchMakingController@scramble');
                Route::post('/matchmaking/{match}/finalize', 'MatchMakingController@finalize');
                Route::delete('/matchmaking/{match}', 'MatchMakingController@destroy');
            });

            /**
             * Payments
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/payment/checkout', 'PaymentsController@showCheckout');
                Route::get('/payment/review/{paymentGateway}', 'PaymentsController@showReview');
                Route::get('/payment/details/{paymentGateway}', 'PaymentsController@showDetails');
                Route::post('/payment/delivery', 'PaymentsController@delivery');
                Route::get('/payment/delivery/{paymentGateway}', 'PaymentsController@showDelivery');
                Route::get('/payment/callback', 'PaymentsController@process');
                Route::post('/payment/post', 'PaymentsController@post');
                Route::get('/payment/failed', 'PaymentsController@showFailed');
                Route::get('/payment/cancelled', 'PaymentsController@showCancelled');
                Route::get('/payment/successful/{purchase}', 'PaymentsController@showSuccessful');
                Route::get('/payment/pending/{purchase}', 'PaymentsController@showPending');
            });

            /**
             * Seating
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::post('/events/{event}/seating/{seatingPlan}', 'Events\SeatingController@store');
                Route::delete('/events/{event}/seating/{seatingPlan}', 'Events\SeatingController@destroy');
            });

            /**
             * Search
             */
            Route::get('/search/users/autocomplete', 'SearchController@usersAutocomplete')->name('autocomplete');

            /**
             * Polls
             */
            Route::get('/polls', 'PollsController@index');
            Route::get('/polls/{poll}', 'PollsController@show');
            Route::group(['middleware' => ['auth', 'banned', 'verified']], function () {
                Route::post('/polls/{poll}/options', 'PollsController@storeOption');
                Route::get('/polls/{poll}/options/{option}/vote', 'PollsController@vote');
                Route::get('/polls/{poll}/options/{option}/abstain', 'PollsController@abstain');
            });

            /**
             * Shop
             */
            Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
                Route::get('/shop/orders', 'ShopController@showAllOrders');
                Route::get('/shop/orders/{order}', 'ShopController@showOrder');
            });
            Route::get('/shop', 'ShopController@index');
            Route::get('/shop/basket', 'ShopController@showBasket');
            Route::post('/shop/basket', 'ShopController@updateBasket');
            Route::get('/shop/{category}', 'ShopController@showCategory');
            Route::get('/shop/{category}/{item}', 'ShopController@showItem');
        });

        /**
         * Admin
         */
        Route::group(['middleware' => ['web', 'admin']], function () {

            /**
             * Index Page
             */
            Route::get('/admin', 'Admin\AdminController@index');

            /**
             * Events
             */
            Route::get('/admin/events', 'Admin\Events\EventsController@index');
            Route::post('/admin/events', 'Admin\Events\EventsController@store');
            Route::get('/admin/events/{event}', 'Admin\Events\EventsController@show');
            Route::post('/admin/events/{event}', 'Admin\Events\EventsController@update');
            Route::delete('/admin/events/{event}', 'Admin\Events\EventsController@destroy');
            Route::post('/admin/events/{event}/information', 'Admin\Events\InformationController@store');
            Route::post('/admin/information/{information}', 'Admin\Events\InformationController@update');
            Route::delete('/admin/information/{information}', 'Admin\Events\InformationController@destroy');
            /**
             * Seating
             */
            Route::get('/admin/events/{event}/seating', 'Admin\Events\SeatingController@index');
            Route::post('/admin/events/{event}/seating', 'Admin\Events\SeatingController@store');
            Route::get('/admin/events/{event}/seating/{seatingPlan}', 'Admin\Events\SeatingController@show');
            Route::post('/admin/events/{event}/seating/{seatingPlan}', 'Admin\Events\SeatingController@update');
            Route::delete('/admin/events/{event}/seating/{seatingPlan}', 'Admin\Events\SeatingController@destroy');
            Route::post('/admin/events/{event}/seating/{seatingPlan}/seat', 'Admin\Events\SeatingController@storeSeat');
            Route::delete('/admin/events/{event}/seating/{seatingPlan}/seat', 'Admin\Events\SeatingController@destroySeat');

            /**
             * Timetables
             */
            Route::get('/admin/events/{event}/timetables', 'Admin\Events\TimetablesController@index');
            Route::post('/admin/events/{event}/timetables', 'Admin\Events\TimetablesController@store');
            Route::get('/admin/events/{event}/timetables/{timetable}', 'Admin\Events\TimetablesController@show');
            Route::post('/admin/events/{event}/timetables/{timetable}', 'Admin\Events\TimetablesController@update');
            Route::delete('/admin/events/{event}/timetables/{timetable}', 'Admin\Events\TimetablesController@destroy');
            Route::post('/admin/events/{event}/timetables/{timetable}/data', 'Admin\Events\TimetableDataController@store');
            Route::post(
                '/admin/events/{event}/timetables/{timetable}/data/{data}',
                'Admin\Events\TimetableDataController@update'
            );
            Route::delete('/admin/events/{event}/timetables/{timetable}/data/{data}', 'Admin\Events\TimetableDataController@destroy');


            /**
             * Tournaments
             */
            Route::get('/admin/events/{event}/tournaments', 'Admin\Events\TournamentsController@index');
            Route::post('/admin/events/{event}/tournaments', 'Admin\Events\TournamentsController@store');
            Route::get('/admin/events/{event}/tournaments/{tournament}', 'Admin\Events\TournamentsController@show');
            Route::post('/admin/events/{event}/tournaments/{tournament}', 'Admin\Events\TournamentsController@update');
            Route::delete('/admin/events/{event}/tournaments/{tournament}', 'Admin\Events\TournamentsController@destroy');
            Route::post('/admin/events/{event}/tournaments/{tournament}/start', 'Admin\Events\TournamentsController@start');
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/finalize',
                'Admin\Events\TournamentsController@finalize'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/enableliveediting',
                'Admin\Events\TournamentsController@enableliveediting'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/disableliveediting',
                'Admin\Events\TournamentsController@disableliveediting'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/addteam',
                'Admin\Events\TournamentsController@addTeam'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/match',
                'Admin\Events\TournamentsController@updateMatch'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/match/{challongeMatchId}',
                'Admin\Events\TournamentsMatchServerController@store'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/match/{challongeMatchId}/update',
                'Admin\Events\TournamentsMatchServerController@update'
            );
            Route::delete(
                '/admin/events/{event}/tournaments/{tournament}/match/{challongeMatchId}/delete',
                'Admin\Events\TournamentsMatchServerController@destroy'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/participants/{participant}/team',
                'Admin\Events\TournamentsController@updateParticipantTeam'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/participants/{participant}/remove',
                'Admin\Events\TournamentsController@unregisterParticipant'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/participants/{participant}/addpug',
                'Admin\Events\TournamentsController@addPug'
            );
            Route::post(
                '/admin/events/{event}/tournaments/{tournament}/participants/{participant}/addsingle',
                'Admin\Events\TournamentsController@addSingle'
            );


            // TODO - REMOVE THIS AND ALL LIKE IT
            /**
             * Legacy
             */
            Route::get('/admin/events/tournaments/fix', 'Admin\Events\TournamentsController@fixScores');


            /**
             * Games
             */
            Route::get('/admin/games', 'Admin\GamesController@index');
            Route::post('/admin/games', 'Admin\GamesController@store');
            Route::get('/admin/games/{game}', 'Admin\GamesController@show');
            Route::post('/admin/games/{game}', 'Admin\GamesController@update');
            Route::delete('/admin/games/{game}', 'Admin\GamesController@destroy');

            /**
             * GameServers
             */
            Route::get('/admin/games/{game}/gameservers', 'Admin\GameServersController@index');
            Route::post('/admin/games/{game}/gameservers', 'Admin\GameServersController@store');
            Route::get('/admin/games/{game}/gameservers/{gameServer}', 'Admin\GameServersController@show');
            Route::post('/admin/games/{game}/gameservers/{gameServer}/updatetoken', 'Admin\GameServersController@updatetoken');
            Route::post('/admin/games/{game}/gameservers/{gameServer}', 'Admin\GameServersController@update');
            Route::delete('/admin/games/{game}/gameservers/{gameServer}', 'Admin\GameServersController@destroy');

            /**
             * GameServerCommands
             */
            Route::get('/admin/games/{game}/gameservercommands', 'Admin\GameServerCommandsController@index');
            Route::post('/admin/games/{game}/gameservercommands', 'Admin\GameServerCommandsController@store');
            Route::get('/admin/games/{game}/gameservercommands/{gameServerCommand}', 'Admin\GameServerCommandsController@show');
            Route::post('/admin/games/{game}/gameservercommands/{gameServerCommand}', 'Admin\GameServerCommandsController@update');
            Route::delete('/admin/games/{game}/gameservercommands/{gameServerCommand}', 'Admin\GameServerCommandsController@destroy');
            Route::post('/admin/games/{game}/gameservercommands/execute/{gameServer}', 'Admin\GameServerCommandsController@executeGameServerCommand');
            Route::post('/admin/games/{game}/gameservercommands/execute/{gameServer}/tournament/{tournament}', 'Admin\GameServerCommandsController@executeGameServerTournamentMatchCommand');
            Route::post('/admin/games/{game}/gameservercommands/execute/{gameServer}/matchmaking/{match}', 'Admin\GameServerCommandsController@executeGameServerMatchMakingCommand');

            /**
             * GameServerCommandParametrs
             */
            Route::post('/admin/games/{game}/gameservercommandparameters', 'Admin\GameServerCommandParametersController@store');
            Route::post('/admin/games/{game}/gameservercommandparameters/{gameServerCommandParameter}', 'Admin\GameServerCommandParametersController@update');
            Route::delete('/admin/games/{game}/gameservercommandparameters/{gameServerCommandParameter}', 'Admin\GameServerCommandParametersController@destroy');


            /**
             * MatchReplays
             */
            Route::delete('/admin/replays/{matchReplay}', 'Admin\MatchReplayController@destroy');


            /**
             * Participants
             */
            Route::get('/admin/events/{event}/participants', 'Admin\Events\ParticipantsController@index');
            Route::get('/admin/events/{event}/participants/signoutall', 'Admin\Events\ParticipantsController@signoutall');
            Route::get('/admin/events/{event}/participants/{participant}', 'Admin\Events\ParticipantsController@show');
            Route::post('/admin/events/{event}/participants/{participant}', 'Admin\Events\ParticipantsController@update');
            Route::post(
                '/admin/events/{event}/participants/{participant}/signin',
                'Admin\Events\ParticipantsController@signIn'
            );
            Route::post(
                '/admin/events/{event}/participants/{participant}/transfer',
                'Admin\Events\ParticipantsController@transfer'
            );

            /**
             * Announcements
             */
            Route::post('/admin/events/{event}/announcements', 'Admin\Events\AnnouncementsController@store');
            Route::post(
                '/admin/events/{event}/announcements/{announcement}',
                'Admin\Events\AnnouncementsController@update'
            );
            Route::delete(
                '/admin/events/{event}/announcements/{announcement}',
                'Admin\Events\AnnouncementsController@destroy'
            );

            /**
             * Mailing
             */
            Route::get('/admin/mailing', 'Admin\MailingController@index');
            Route::get('/admin/mailing/{mailTemplate}', 'Admin\MailingController@show');
            Route::post('/admin/mailing', 'Admin\MailingController@store');
            Route::post('/admin/mailing/{mailTemplate}/send', 'Admin\MailingController@send');
            Route::post('/admin/mailing/{mailTemplate}', 'Admin\MailingController@update');
            Route::delete('/admin/mailing/{mailTemplate}', 'Admin\MailingController@destroy');


            /**
             * Tickets
             */
            Route::get('/admin/events/{event}/tickets', 'Admin\Events\TicketsController@index');
            Route::post('/admin/events/{event}/tickets', 'Admin\Events\TicketsController@store');
            Route::get('/admin/events/{event}/tickets/{ticket}', 'Admin\Events\TicketsController@show');
            Route::post('/admin/events/{event}/tickets/{ticket}', 'Admin\Events\TicketsController@update');
            Route::delete('/admin/events/{event}/tickets/{ticket}', 'Admin\Events\TicketsController@destroy');

            /**
             * Gifts
             */
            Route::post('/admin/events/{event}/freebies/admin', 'Admin\Events\EventsController@freeStaff');
            Route::post('/admin/events/{event}/freebies/gift', 'Admin\Events\EventsController@freeGift');

            /**
             * Sponsors
             */
            Route::post('/admin/events/{event}/sponsors', 'Admin\Events\SponsorsController@store');
            Route::post('/admin/events/{event}/sponsors/{sponsor}', 'Admin\Events\SponsorsController@update');
            Route::delete('/admin/events/{event}/sponsors/{sponsor}', 'Admin\Events\SponsorsController@destroy');

            /**
             * Venues
             */
            Route::get('/admin/venues', 'Admin\Events\VenuesController@index');
            Route::post('/admin/venues', 'Admin\Events\VenuesController@store');
            Route::get('/admin/venues/{venue}', 'Admin\Events\VenuesController@show');
            Route::post('/admin/venues/{venue}', 'Admin\Events\VenuesController@update');
            Route::delete('/admin/venues/{venue}', 'Admin\Events\VenuesController@destroy');
            Route::post('/admin/venues/{venue}/{image}', 'Admin\Events\VenuesController@updateImage');
            Route::delete('/admin/venues/{venue}/{image}', 'Admin\Events\VenuesController@destroyImage');

            /**
             * Galleries
             */
            Route::get('/admin/gallery', 'Admin\GalleryController@index');
            Route::post('/admin/gallery', 'Admin\GalleryController@store');
            Route::get('/admin/gallery/{album}', 'Admin\GalleryController@show');
            Route::post('/admin/gallery/{album}', 'Admin\GalleryController@update');
            Route::delete('/admin/gallery/{album}', 'Admin\GalleryController@destroy');
            Route::post('/admin/gallery/{album}/upload', 'Admin\GalleryController@uploadFile');
            Route::post('/admin/gallery/{album}/{image}', 'Admin\GalleryController@updateFile');
            Route::delete('/admin/gallery/{album}/{image}', 'Admin\GalleryController@destroyFile');

            /**
             * Help
             */
            Route::get('/admin/help', 'Admin\HelpController@index');
            Route::post('/admin/help', 'Admin\HelpController@store');
            Route::get('/admin/help/{helpCategory}', 'Admin\HelpController@show');
            Route::post('/admin/help/{helpCategory}', 'Admin\HelpController@update');
            Route::delete('/admin/help/{helpCategory}', 'Admin\HelpController@destroy');
            Route::post('/admin/help/{helpCategory}/{entry}/upload', 'Admin\HelpController@uploadFiles');
            Route::post('/admin/help/{helpCategory}/{entry}/{attachment}', 'Admin\HelpController@updateFile');
            Route::delete('/admin/help/{helpCategory}/{entry}/{attachment}', 'Admin\HelpController@destroyFile');
            Route::post('/admin/help/{helpCategory}/add', 'Admin\HelpController@addHelpEntry');
            Route::post('/admin/help/{helpCategory}/{entry}', 'Admin\HelpController@updateHelpEntry');
            Route::delete('/admin/help/{helpCategory}/{entry}', 'Admin\HelpController@destroyHelpEntry');

            /**
             * Users
             */
            Route::get('/admin/users', 'Admin\UsersController@index');
            Route::get('/admin/users/{user}', 'Admin\UsersController@show');
            Route::delete('/admin/users/{user}', 'Admin\UsersController@remove');
            Route::post('/admin/users/{user}/admin', 'Admin\UsersController@grantAdmin');
            Route::delete('/admin/users/{user}/admin', 'Admin\UsersController@removeAdmin');
            Route::post('/admin/users/{user}/ban', 'Admin\UsersController@ban');
            Route::post('/admin/users/{user}/unban', 'Admin\UsersController@unban');
            Route::post('/admin/users/{user}/setemailverification', 'Admin\UsersController@setemailverification');
            Route::post('/admin/users/{user}/removemailverification', 'Admin\UsersController@removemailverification');
            Route::post('/admin/users/{user}/unauthorizeThirdparty/{method}', 'Admin\UsersController@unauthorizeThirdparty');

            /**
             * MatchMaking
             */
            Route::get('/admin/matchmaking', 'Admin\MatchMakingController@index');
            Route::post('/admin/matchmaking/{match}/serverstore', 'Admin\MatchMakingServerController@store');
            Route::post('/admin/matchmaking/{match}/serverupdate', 'Admin\MatchMakingServerController@update');
            Route::delete('/admin/matchmaking/{match}/serverdelete', 'Admin\MatchMakingServerController@destroy');
            Route::get('/admin/matchmaking/{match}', 'Admin\MatchMakingController@show');
            Route::post('/admin/matchmaking', 'Admin\MatchMakingController@store');
            Route::post('/admin/matchmaking/{match}/team/{team}/teamplayer/add', 'Admin\MatchMakingController@addusertomatch');
            Route::delete('/admin/matchmaking/{match}/team/{team}/teamplayer/{teamplayer}/delete', 'Admin\MatchMakingController@deleteuserfrommatch');
            Route::post('/admin/matchmaking/{match}/team/add', 'Admin\MatchMakingController@addteam');
            Route::post('/admin/matchmaking/{match}/team/{team}/update', 'Admin\MatchMakingController@updateteam');
            Route::delete('/admin/matchmaking/{match}/team/{team}/delete', 'Admin\MatchMakingController@deleteteam');
            Route::post('/admin/matchmaking/{match}/update', 'Admin\MatchMakingController@update');
            Route::post('/admin/matchmaking/{match}/start', 'Admin\MatchMakingController@start');
            Route::post('/admin/matchmaking/{match}/open', 'Admin\MatchMakingController@open');
            Route::post('/admin/matchmaking/{match}/finalize', 'Admin\MatchMakingController@finalize');
            Route::delete('/admin/matchmaking/{match}', 'Admin\MatchMakingController@destroy');

            /**
             * Settings
             */
            Route::get('/admin/settings', 'Admin\SettingsController@index');
            Route::post('/admin/settings', 'Admin\SettingsController@update');
            Route::get('/admin/settings/org', 'Admin\SettingsController@showOrg');
            Route::get('/admin/settings/payments', 'Admin\SettingsController@showPayments');
            Route::get('/admin/settings/systems', 'Admin\SettingsController@showSystems');
            Route::post('/admin/settings/systems', 'Admin\SettingsController@updateSystems');
            Route::get('/admin/settings/auth', 'Admin\SettingsController@showAuth');
            Route::get('/admin/settings/api', 'Admin\SettingsController@showApi');
            Route::post('/admin/settings/api', 'Admin\SettingsController@updateApi');
            Route::get('/admin/settings/link/{social}', 'Admin\SettingsController@linkSocial');
            Route::delete('/admin/settings/unlink/{social}', 'Admin\SettingsController@unlinkSocial');
            Route::post('/admin/settings/payments/{gateway}/disable', 'Admin\SettingsController@disablePaymentGateway');
            Route::post('/admin/settings/payments/{gateway}/enable', 'Admin\SettingsController@enablePaymentGateway');
            Route::post('/admin/settings/login/{method}/disable', 'Admin\SettingsController@disableLoginMethod');
            Route::post('/admin/settings/login/{method}/enable', 'Admin\SettingsController@enableLoginMethod');
            Route::post('/admin/settings/auth/general', 'Admin\SettingsController@updateAuthGeneral');
            Route::post('/admin/settings/auth/steam', 'Admin\SettingsController@updateAuthSteam');
            Route::post('/admin/settings/credit/enable', 'Admin\SettingsController@enableCreditSystem');
            Route::post('/admin/settings/credit/disable', 'Admin\SettingsController@disableCreditSystem');
            Route::post('/admin/settings/shop/enable', 'Admin\SettingsController@enableShopSystem');
            Route::post('/admin/settings/shop/disable', 'Admin\SettingsController@disableShopSystem');
            Route::post('/admin/settings/gallery/enable', 'Admin\SettingsController@enableGallerySystem');
            Route::post('/admin/settings/gallery/disable', 'Admin\SettingsController@disableGallerySystem');
            Route::post('/admin/settings/help/enable', 'Admin\SettingsController@enableHelpSystem');
            Route::post('/admin/settings/help/disable', 'Admin\SettingsController@disableHelpSystem');
            Route::post('/admin/settings/matchmaking/enable', 'Admin\SettingsController@enableMatchMakingSystem');
            Route::post('/admin/settings/matchmaking/disable', 'Admin\SettingsController@disableMatchMakingSystem');
            Route::post('/admin/settings/generate/qr', 'Admin\SettingsController@regenerateQRCodes');
            Route::post('/admin/settings/generate/newqr', 'Admin\SettingsController@regenerateQRCodesWithNewNames');

            /**
             * Appearance
             */
            Route::get('/admin/settings/appearance', 'Admin\AppearanceController@index');
            Route::post('/admin/settings/appearance/slider/images/', 'Admin\AppearanceController@sliderUpload');
            Route::post('/admin/settings/appearance/slider/images/{image}', 'Admin\AppearanceController@sliderUpdate');
            Route::delete('/admin/settings/appearance/slider/images/{image}', 'Admin\AppearanceController@sliderDelete');
            Route::get('/admin/settings/appearance/css/recompile', 'Admin\AppearanceController@cssRecompile');
            Route::get('/admin/settings/appearance/css/updatedatabasefromfile', 'Admin\AppearanceController@cssUpdateDatabaseFromFile');
            Route::post('/admin/settings/appearance/css/override', 'Admin\AppearanceController@cssOverride');
            Route::post('/admin/settings/appearance/css/variables', 'Admin\AppearanceController@cssVariables');

            /**
             * News
             */
            Route::get('/admin/news', 'Admin\NewsController@index');
            Route::post('/admin/news', 'Admin\NewsController@store');
            Route::get('/admin/news/{newsArticle}', 'Admin\NewsController@show');
            Route::post('/admin/news/{newsArticle}', 'Admin\NewsController@update');
            Route::delete('/admin/news/{newsArticle}', 'Admin\NewsController@destroy');
            Route::get('/admin/news/{newsArticle}/comments/{newsComment}/delete', 'Admin\NewsController@destroyComment');
            Route::get('/admin/news/{newsArticle}/comments/{newsComment}/approve', 'Admin\NewsController@approveComment');
            Route::get('/admin/news/{newsArticle}/comments/{newsComment}/reject', 'Admin\NewsController@rejectComment');
            Route::get(
                '/admin/news/{newsArticle}/comments/{newsComment}/reports/{newsCommentReport}/delete',
                'Admin\NewsController@destroyReport'
            );

            /**
             * Polls
             */
            Route::get('/admin/polls', 'Admin\PollsController@index');
            Route::post('/admin/polls', 'Admin\PollsController@store');
            Route::get('/admin/polls/{poll}', 'Admin\PollsController@show');
            Route::post('/admin/polls/{poll}', 'Admin\PollsController@update');
            Route::post('/admin/polls/{poll}/end', 'Admin\PollsController@endPoll');
            Route::delete('/admin/polls/{poll}', 'Admin\PollsController@destroy');
            Route::post('/admin/polls/{poll}/options', 'Admin\PollsController@storeOption');
            Route::delete('/admin/polls/{poll}/options/{option}', 'Admin\PollsController@destroyOption');

            /**
             * Purchases
             */
            Route::get('/admin/purchases', 'Admin\PurchasesController@index');
            Route::get('/admin/purchases/shop', 'Admin\PurchasesController@showShop');
            Route::get('/admin/purchases/event', 'Admin\PurchasesController@showEvent');
            Route::get('/admin/purchases/{purchase}/setSuccess', 'Admin\PurchasesController@setSuccess');
            Route::get('/admin/purchases/{purchase}', 'Admin\PurchasesController@show');

            /**
             * Credit System
             */
            Route::get('/admin/credit', 'Admin\CreditController@index');
            Route::post('/admin/credit/edit', 'Admin\CreditController@edit');
            Route::post('/admin/credit/settings', 'Admin\CreditController@settings');

            /**
             * Shop
             */
            Route::get('/admin/shop', 'Admin\ShopController@index');
            Route::post('/admin/shop/item', 'Admin\ShopController@storeItem');
            Route::post('/admin/shop/category', 'Admin\ShopController@storeCategory');
            Route::get('/admin/shop/{category}', 'Admin\ShopController@showCategory');
            Route::post('/admin/shop/{category}', 'Admin\ShopController@updateCategory');
            Route::delete('/admin/shop/{category}', 'Admin\ShopController@deleteCategory');
            Route::get('/admin/shop/{category}/{item}', 'Admin\ShopController@showItem');
            Route::post('/admin/shop/{category}/{item}', 'Admin\ShopController@updateItem');
            Route::delete('/admin/shop/{category}/{item}', 'Admin\ShopController@deleteItem');
            Route::post('/admin/shop/{category}/{item}/images', 'Admin\ShopController@uploadItemImage');
            Route::post('/admin/shop/{category}/{item}/images/{image}', 'Admin\ShopController@updateItemImage');
            Route::delete('/admin/shop/{category}/{item}/images/{image}', 'Admin\ShopController@deleteItemImage');

            /**
             * Orders
             */
            Route::get('/admin/orders', 'Admin\OrdersController@index');
            Route::get('/admin/orders/{order}', 'Admin\OrdersController@show');
            Route::post('/admin/orders/{order}/processing', 'Admin\OrdersController@setAsProcessing');
            Route::post('/admin/orders/{order}/shipped', 'Admin\OrdersController@setAsShipped');
            Route::post('/admin/orders/{order}/tracking', 'Admin\OrdersController@updateTrackingDetails');
            Route::post('/admin/orders/{order}/complete', 'Admin\OrdersController@setAsComplete');
            Route::post('/admin/orders/{order}/cancel', 'Admin\OrdersController@setAsCancelled');
        });
    });
});

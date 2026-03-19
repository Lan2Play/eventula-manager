<?php

/**
 * Web Routes
 *
 * Public frontend, authentication, install, and user-facing routes.
 * Migrated from app/Http/routes.php — see that file for migration notes.
 */

/**
 * phpinfo in debug mode
 */
if (config('app.debug') === true) {
    Route::get('phpinfo', function () {
        phpinfo();
    })->name('phpinfo');
}

/**
 * Image Converter
 */
Route::get('{image}', 'Api\Images\WebpController@convert')->where('image', '.*\.webp');

/**
 * Install
 */
Route::group(['middleware' => ['web', 'notInstalled']], function () {
    Route::get('/install', 'InstallController@installation');
    Route::post('/install', 'InstallController@install');
});

Route::group(['middleware' => ['installed']], function () {

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
            Route::get('/account/sso/remove/{method}', 'AccountController@showRemoveSso');
            Route::post('/account/sso/remove/{method}', 'AccountController@removeSso');
            Route::get('/account/sso/add/{method}', 'AccountController@addSso');
            Route::get('/account/tokens/wizzard/start/{application?}/{callbackurl?}', 'AccountController@showTokenWizzardStart');
            Route::post('/account/tokens/wizzard/finish', 'AccountController@showTokenWizzardFinish');
            Route::post('/account/tokens/add', 'AccountController@addToken');
            Route::delete('/account/tokens/remove/{token}', 'AccountController@removeToken');
            Route::post('/account', 'AccountController@update');
            Route::post('/account/delete', 'Auth\SteamController@destroy');
            Route::post('/account/avatar/selected', 'AccountController@update_selected_avatar');
            Route::post('/account/avatar', 'AccountController@update_local_avatar');
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
         * Audits
         */
        Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
            Route::get('/audits/ticket/{ticket}', 'AuditController@showAuditsForTickets');
        });


        /**
         * Events
         */
        Route::get('/events', 'Events\EventsController@index');
        Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
            Route::get('/events/participants/{ticket}/{fileType}', 'Events\TicketController@exportParticipantAsFile');
        });
        Route::get('/events/{event}', 'Events\EventsController@show');
        Route::get('/events/{event}/big', 'HomeController@bigScreen');
        Route::get('/events/{event}/generate-ics', 'Events\EventsController@generateICS')->name('generate-event-ics');

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
            Route::get('/tickets/retrieve/{ticket}', 'Events\TicketTypeController@retrieve');
            Route::post('/tickets/purchase/{ticketType}', 'Events\TicketTypeController@purchase');
        });

        /**
         * Gifts
         */
        Route::group(['middleware' => ['auth', 'banned', 'verified', 'nophonenumber']], function () {
            Route::get('/gift/accept', 'Events\TicketController@acceptGift');
            Route::post('/gift/{ticket}', 'Events\TicketController@gift');
            Route::post('/gift/{ticket}/revoke', 'Events\TicketController@revokeGift');
            Route::post('/events/{event}/participants/{ticket}', 'Events\TicketController@update');
            Route::post('/events/{event}/participants/{ticket}/resetManager', 'Events\TicketController@resetManager');
            Route::post('/events/{event}/participants/{ticket}/resetUser', 'Events\TicketController@resetUser');
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
});

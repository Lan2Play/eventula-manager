<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\TrustProxies::class,
        \Spatie\CookieConsent\CookieConsentMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\Legacywarning::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60000,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'userapi' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:60000,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'installed'         => \App\Http\Middleware\Installed::class,
        'nophonenumber'     => \App\Http\Middleware\NoPhoneNumber::class,
        'nodebugbar'        => \App\Http\Middleware\NoDebugBar::class,
        'notInstalled'      => \App\Http\Middleware\NotInstalled::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin'             => \App\Http\Middleware\Admin::class,
        'banned'            => \App\Http\Middleware\Banned::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'bindings'          => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'language'          => \App\Http\Middleware\LanguageSwitcher::class,
        'gameserver'        => \App\Http\Middleware\Gameserver::class,
    ];
}

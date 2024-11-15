<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Helpers;
use View;
use Auth;
use URL;
use App\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Paginator::useBootstrap();

        view()->composer('layouts._partials.events-navigation', function ($view) {
            $view->with('events', Event::orderBy('display_name', 'desc')->get());
        });
        view()->composer('*', function ($view) {
            $view->with('user', Auth::user());
        });

        // Pull API Keys
        if (env('ENV_OVERRIDE')) {
            // From ENV File
            // Paypal
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.username', env('PAYPAL_USERNAME'));
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.password', env('PAYPAL_PASSWORD'));
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.signature', env('PAYPAL_SIGNATURE'));
            // Stripe
            @\Config::set('laravel-omnipay.gateways.stripe.credentials.public', env('STRIPE_PUBLIC_KEY'));
            @\Config::set('laravel-omnipay.gateways.stripe.credentials.secret', env('STRIPE_SECRET_KEY'));
            // Challonge
            @\Config::set('challonge.api_key', env('CHALLONGE_API_KEY'));
            // Steam
            @\Config::set('steam-auth.api_key', env('STEAM_API_KEY'));
        } elseif (\Schema::hasTable('api_keys')) {
            // From Database
            // Paypal
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.username', \App\ApiKey::where('key', 'paypal_username')->first()->value);
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.password', \App\ApiKey::where('key', 'paypal_password')->first()->value);
            @\Config::set('laravel-omnipay.gateways.paypal_express.credentials.signature', \App\ApiKey::where('key', 'paypal_signature')->first()->value);
            // Stripe
            @\Config::set('laravel-omnipay.gateways.stripe.credentials.public', \App\ApiKey::where('key', 'stripe_public_key')->first()->value);
            @\Config::set('laravel-omnipay.gateways.stripe.credentials.secret', \App\ApiKey::where('key', 'stripe_secret_key')->first()->value);
            // Challonge
            @\Config::set('challonge.api_key', \App\ApiKey::where('key', 'challonge_api_key')->first()->value);
            // Steam
            @\Config::set('steam-auth.api_key', \App\ApiKey::where('key', 'steam_api_key')->first()->value);
        }


        if (\Schema::hasTable('settings')) {
            foreach (\App\Setting::all() as $setting) {
                @\Config::set('settings.'.$setting->setting, $setting->value);
            }
        }

        // Set SEO Defaults
        @\Config::set('seotools.meta.defaults.description', Helpers::getSeoDescription());
        if (config('settings.seo_keywords') != null) {
            @\Config::set('seotools.meta.defaults.keywords', Helpers::getSeoKeywords());
        }
        @\Config::set('seotools.opengraph.defaults.description', Helpers::getSeoDescription());
        @\Config::set('seotools.opengraph.defaults.site_name', config('settings.org_name'));

        // Foce HTTPS if required
        if (env('ENABLE_HTTPS') || env('FORCE_APP_HTTPS')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug') === true) {
            $this->app->register(\KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Orangehill\Iseed\IseedServiceProvider::class);
        }

        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}

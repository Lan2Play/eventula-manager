<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Setting;
use App\Appearance;
use App\SliderImage;
use App\ApiKey;

use Spatie\MailTemplates\Models\MailTemplate;


use App\Mail\EventulaTicketOrderMail;
use App\Mail\EventulaTicketOrderPendingMail;
use App\Mail\EventulaTicketOrderPaymentFinishedMail;

use Faker\Factory as Faker;

class RequiredDatabaseSeeder extends Seeder
{
    private $settings = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        ## House Cleaning
        // DB::table('settings')->truncate();

        ## Settings
        Setting::firstOrCreate(
            ['setting'          => 'org_name'],
            [
                'value'         => env('APP_NAME', 'OrgNameHere'),
                'default'       => true,
                'description'   => 'Name of the Organization'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'org_tagline'],
            [
                'value'         => env('APP_TAGLINE', 'Tagline Here'),
                'default'       => true,
                'description'   => 'Tagline of the Organization'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'seo_keywords'],
            [
                'value'         => env('SEO_KEYWORDS', "Events,Eventula,Th0rn0"),
                'default'       => true,
                'description'   => 'Keywords for the Organization SEO'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'org_logo'],
            [
                'value'         => env('APP_LOGO', '/storage/images/main/logo_main.png'),
                'default'       => true,
                'description'   => 'Organization Logo'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'org_favicon'],
            [
                'value'         => env('APP_FAVICON', '/storage/images/main/favicon.ico'),
                'default'       => true,
                'description'   => 'Organization Favicon'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'purchase_terms_and_conditions'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
                'description'   => 'T&Cs to be displayed on the checkout page'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'registration_terms_and_conditions'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
                'description'   => 'T&Cs to be displayed on the registration page'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'steam_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'Link to your Steam Group'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'teamspeak_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'IP to your Teamspeak Server'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'mumble_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'IP to your Mumble Server'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'discord_id'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'ID for Discord Widget'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'discord_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'Link to your Discord Server'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'reddit_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'Link to your Subreddit'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'twitter_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'Link to your Twitter Account'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'facebook_link'],
            [
                'value'         => null,
                'default'       => true,
                'description'   => 'Link to your Facebook Page'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'frontpage_alot_tagline'],
            [
                'value'         => "People we've fragged",
                'default'       => true,
                'description'   => "Appears on the frontpage on the banner above the footer"
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'participant_count_offset'],
            [
                'value'         => 0,
                'default'       => true,
                'description'   => 'Increment the Total Participant Count on the Home page'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'event_count_offset'],
            [
                'value'         => 0,
                'default'       => true,
                'description'   => 'Increment the Total Event Count on the Home page'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'currency'],
            [
                'value'         => 'GBP',
                'default'       => true,
                'description'   => 'Currency to use site wide. Only one can be used'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'about_main'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'about_short'],
            [
                'value'         => $faker->paragraph($nbSentences = 4, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'about_our_aim'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'about_who'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'legal_notice'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'privacy_policy'],
            [
                'value'         => $faker->paragraph($nbSentences = 90, $variableNbSentences = true),
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'payment_gateway_stripe'],
            [
                'value'         => true,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'payment_gateway_paypal_express'],
            [
                'value'         => true,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'payment_gateway_free'],
            [
                'value'         => true,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'payment_gateway_onsite'],
            [
                'value'         => true,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_enabled'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_tournament_participation'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_tournament_first'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_tournament_second'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_tournament_third'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_registration_event'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'credit_award_registration_site'],
            [
                'value'         => 0,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'login_standard'],
            [
                'value'         => true,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'login_steam'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'shop_status'],
            [
                'value'         => 'OPEN',
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'shop_welcome_message'],
            [
                'value'         => "Welcome to the Shop!",
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'shop_closed_message'],
            [
                'value'         => "Shop is currently closed!",
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'shop_enabled'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'gallery_enabled'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'help_enabled'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'matchmaking_enabled'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'installed'],
            [
                'value'         => false,
                'default'       => true,
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'site_locale'],
            [
                'value'         => 'en',
                'default'       => true,
                'description'   => 'Locale that is used for all the Site Default Texts'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'user_locale_enabled'],
            [
                'value'         => true,
                'default'       => true,
                'description'   => 'Enable Users to set different locale in their profile settings'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'systems_matchmaking_publicuse'],
            [
                'value'         => true,
                'default'       => true,
                'description'   => 'matchmaking is globally usable for all registered users or only inside Events'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'systems_matchmaking_maxopenperuser'],
            [
                'value'         => 0,
                'default'       => true,
                'description'   => 'Maximal open matchmaking matches per user'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'auth_steam_require_email'],
            [
                'value'         => false,
                'default'       => true,
                'description'   => 'Require email for a steam authenticated user'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'auth_allow_email_change'],
            [
                'value'         => true,
                'default'       => true,
                'description'   => 'allow user to change the Email Address'
            ]
        );
        Setting::firstOrCreate(
            ['setting'          => 'auth_require_phonenumber'],
            [
                'value'         => false,
                'default'       => true,
                'description'   => 'Require phonenumber for all users'
            ]
        );

        ## Apperance
        Appearance::firstOrCreate(
            ['key'   => 'color_primary'],
            [
                'value' => 'orange',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_primary_text'],
            [
                'value' => 'white',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_secondary'],
            [
                'value' => '#333',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_secondary_text'],
            [
                'value' => 'white',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_body_links'],
            [
                'value' => 'orange',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_body_background'],
            [
                'value' => '#fff',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_body_text'],
            [
                'value' => '#333',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_header_background'],
            [
                'value' => '#333',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_header_text'],
            [
                'value' => '#9d9d9d',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_header_links'],
            [
                'value' => '#fff',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_header_links_hover'],
            [
                'value' => 'orange',
                'type'  => 'CSS_VAR',
            ]
        );

        Appearance::firstOrCreate(
            ['key'   => 'color_header_line'],
            [
                'value' => 'orange',
                'type'  => 'CSS_VAR',
            ]
        );

        ## Slider Images
        SliderImage::firstOrCreate(
            ['slider_name'   => 'frontpage'],
            [
                'path'  => '/storage/images/main/slider/frontpage/1.jpg',
                'order' => '4',
            ]
        );

        SliderImage::firstOrCreate(
            ['slider_name'   => 'frontpage'],
            [
                'path'  => '/storage/images/main/slider/frontpage/2.jpg',
                'order' => '1',
            ]
        );

        SliderImage::firstOrCreate(
            ['slider_name'   => 'frontpage'],
            [
                'path'  => '/storage/images/main/slider/frontpage/3.jpg',
                'order' => '2',
            ]
        );

        SliderImage::firstOrCreate(
            ['slider_name'   => 'frontpage'],
            [
                'path'  => '/storage/images/main/slider/frontpage/4.jpg',
                'order' => '5',
            ]
        );

        SliderImage::firstOrCreate(
            ['slider_name'   => 'frontpage'],
            [
                'path'  => '/storage/images/main/slider/frontpage/5.jpg',
                'order' => '3',
            ]
        );

        ## Api Keys
        ApiKey::firstOrCreate(
            ['key'          => 'paypal_username'],
            [
                'value'         => env('PAYPAL_USERNAME', null),
            ]
        );

        ApiKey::firstOrCreate(
            ['key'          => 'paypal_password'],
            [
                'value'         => env('PAYPAL_PASSWORD', null),
            ]
        );

        ApiKey::firstOrCreate(
            ['key'          => 'paypal_signature'],
            [
                'value'         => env('PAYPAL_SIGNATURE', null),
            ]
        );

        ApiKey::firstOrCreate(
            ['key'          => 'stripe_public_key'],
            [
                'value'         => env('STRIPE_PUBLIC_KEY', null),
            ]
        );

        ApiKey::firstOrCreate(
            ['key'          => 'stripe_secret_key'],
            [
                'value'         => env('STRIPE_SECRET_KEY', null),
            ]
        );
        ApiKey::firstOrCreate(
            ['key'          => 'challonge_api_key'],
            [
                'value'         => env('CHALLONGE_API_KEY', null),
            ]
            );
        ApiKey::firstOrCreate(
            ['key'          => 'steam_api_key'],
            [
                'value'         => env('STEAM_API_KEY', null),
            ]
        );

        #Mailing
        MailTemplate::firstOrCreate(
            ['mailable' => EventulaTicketOrderMail::class],
            [
                'subject' => "ticket order",
                'html_template' => '<p>placeholder<p>',
                'text_template' => "placeholder",
            ]
        );
        MailTemplate::firstOrCreate(
            ['mailable' => EventulaTicketOrderPendingMail::class],
            [
                'subject' => "ticket order pending",
                'html_template' => '<p>placeholder<p>',
                'text_template' => "placeholder",
            ]
        );
        MailTemplate::firstOrCreate(
            ['mailable' => EventulaTicketOrderPaymentFinishedMail::class],
            [
                'subject' => "ticket payment finished",
                'html_template' => '<p>placeholder<p>',
                'text_template' => "placeholder",
            ]
        );

    }
}

<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Redirect;
use Settings;
use Colors;

use App\ApiKey;
use App\User;
use App\Setting;
use App\Event;
use App\EventParticipant;
use App\EventTicket;
use App\CreditLog;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Rules\ValidLocale;

use function PHPUnit\Framework\isEmpty;

class SettingsController extends Controller
{
    /**
     * Show Settings Index Page
     * @return Redirect
     */
    public function index()
    {
        return view('admin.settings.index')
            ->with('settings', Setting::all())
            ->with('isShopEnabled', Settings::isShopEnabled())
            ->with('isGalleryEnabled', Settings::isGalleryEnabled())
            ->with('isHelpEnabled', Settings::isHelpEnabled())
            ->with('isMatchMakingEnabled', Settings::isMatchMakingEnabled())
            ->with('isCreditEnabled', Settings::isCreditEnabled())
            ->with('supportedLoginMethods', Settings::getSupportedLoginMethods())
            ->with('activeLoginMethods', Settings::getLoginMethods());
    }

    /**
     * Show Settings Org Page
     * @return Redirect
     */
    public function showOrg()
    {
        return view('admin.settings.org')
            ->with('settings', Setting::all());
    }

    /**
     * Show Settings Payment Page
     * @return Redirect
     */
    public function showPayments()
    {

        return view('admin.settings.payments')
            ->with('supportedPaymentGateways', Settings::getSupportedPaymentGateways())
            ->with('activePaymentGateways', Settings::getPaymentGateways())
            ->with('isCreditEnabled', Settings::isCreditEnabled())
            ->with('isShopEnabled', Settings::isShopEnabled());
    }

    /**
     * Show Settings Opt Systems Page
     * @return Redirect
     */
    public function showSystems()
    {

        return view('admin.settings.systems')
            ->with('isSystemsMatchMakingPublicuseEnabled', Settings::isSystemsMatchMakingPublicuseEnabled())
            ->with('isSystemsMatchMakingNonegameEnabled', Settings::isSystemsMatchMakingNonegameEnabled())
            ->with('maxOpenPerUser', Settings::getSystemsMatchMakingMaxopenperuser())
            ->with('isMatchMakingEnabled', Settings::isMatchMakingEnabled())
            ->with('isCreditEnabled', Settings::isCreditEnabled())
            ->with('creditAwardTournamentParticipation', Settings::getCreditTournamentParticipation())
            ->with('creditAwardTournamentFirst', Settings::getCreditTournamentFirst())
            ->with('creditAwardTournamentSecond', Settings::getCreditTournamentSecond())
            ->with('creditAwardTournamentThird', Settings::getCreditTournamentThird())
            ->with('creditAwardRegistrationEvent', Settings::getCreditRegistrationEvent())
            ->with('creditAwardRegistrationSite', Settings::getCreditRegistrationSite())



            ->with('isShopEnabled', Settings::isShopEnabled())
            ->with('shopWelcomeMessage', Settings::getShopWelcomeMessage())
            ->with('shopStatus', Settings::getShopStatus())
            ->with('shopClosedMessage', Settings::getShopClosedMessage());
    }

    /**
     * Show Settings Index Page
     * @return Redirect
     */
    public function showAuth()
    {

        return view('admin.settings.auth')
            ->with('supportedLoginMethods', Settings::getSupportedLoginMethods())
            ->with('activeLoginMethods', Settings::getLoginMethods())
            ->with('isAuthAllowEmailChangeEnabled', Settings::isAuthAllowEmailChangeEnabled())
            ->with('isAuthSteamRequireEmailEnabled', Settings::isAuthSteamRequireEmailEnabled())
            ->with('isAuthRequirePhonenumberEnabled', Settings::isAuthRequirePhonenumberEnabled());
    }

    /**
     * Show API Index Page
     * @return Redirect
     */
    public function showApi()
    {
        return view('admin.settings.api')
            ->with('apiKeys', ApiKey::all())
            ->with('paypalUsername', ApiKey::where('key', 'paypal_username')->first()->value)
            ->with('paypalPassword', ApiKey::where('key', 'paypal_password')->first()->value)
            ->with('paypalSignature', ApiKey::where('key', 'paypal_signature')->first()->value)
            ->with('stripePublicKey', ApiKey::where('key', 'stripe_public_key')->first()->value)
            ->with('stripeSecretKey', ApiKey::where('key', 'stripe_secret_key')->first()->value)
            ->with('challongeApiKey', ApiKey::where('key', 'challonge_api_key')->first()->value)
            ->with('steamApiKey', ApiKey::where('key', 'steam_api_key')->first()->value);
    }

    /**
     * Update API
     * @param  Request $request
     * @return Redirect
     */
    public function updateApi(Request $request)
    {
        if (isset($request->challonge_api_key) && !ApiKey::setChallongeApiKey($request->challonge_api_key)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->steam_api_key) && !ApiKey::setSteamApiKey($request->steam_api_key)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->paypal_username) && !ApiKey::setPaypalUsername($request->paypal_username)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->paypal_password) && !ApiKey::setPaypalPassword($request->paypal_password)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->paypal_signature) && !ApiKey::setPaypalSignature($request->paypal_signature)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->stripe_public_key) && !ApiKey::setStripePublicKey($request->stripe_public_key)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        if (isset($request->stripe_secret_key) && !ApiKey::setStripeSecretKey($request->stripe_secret_key)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated!');
        return Redirect::back();
    }

    /**
     * Update Systems
     * @param  Request $request
     * @return Redirect
     */
    public function updateSystems(Request $request)
    {

        $rules = [
            'publicuse'                 => 'in:on,off',
            'nonegame'                 => 'in:on,off',
            'maxopenperuser'            => 'numeric',
            'tournament_participation'    => 'filled|integer',
            'tournament_first'            => 'filled|integer',
            'tournament_second'            => 'filled|integer',
            'tournament_third'            => 'filled|integer',
            'registration_event'        => 'filled|integer',
            'registration_site'            => 'filled|integer',
            'shop_status'               => 'in:OPEN,CLOSED',
        ];
        $messages = [
            'publicuse.in'                      => 'Publicuse must be true or false',
            'nonegame.in'                      => 'Nonegame must be true or false',
            'autostart.in'                      => 'autostart must be true or false',
            'maxopenperuser.numeric'            => 'maxopenperuser must be a number',
            'tournament_participation.filled'    => 'Tournament Participantion cannot be empty',
            'tournament_participation.integer'  => 'Tournament Participantion must be a number',
            'tournament_first.filled'             => 'Tournament First cannot be empty',
            'tournament_first.integer'          => 'Tournament First must be a number',
            'tournament_second.filled'             => 'Tournament Second cannot be empty',
            'tournament_second.integer'          => 'Tournament Second must be a number',
            'tournament_third.filled'             => 'Tournament Third cannot be empty',
            'tournament_third.integer'          => 'Tournament Third must be a number',
            'registration_event.filled'         => 'Event Registration cannot be empty',
            'registration_event.integer'          => 'Event Registration must be a number',
            'registration_site.filled'             => 'Site Registration cannot be empty',
            'registration_site.integer'          => 'Site Registration must be a number',
            'shop_status.in'                    => 'Shop Status must be OPEN or CLOSED',
        ];
        $this->validate($request, $rules, $messages);

        if (($request->publicuse ? true : false)) {
            if (!Settings::enableSystemsMatchMakingPublicuse()) {
                Session::flash('alert-danger', "Could not Enable the MatchMaking System Publicuse!");
                return Redirect::back();
            }
        } else {
            if (!Settings::disableSystemsMatchMakingPublicuse()) {
                Session::flash('alert-danger', "Could not Disable the MatchMaking System Publicuse!");
                return Redirect::back();
            }
        }

        if (($request->nonegame ? true : false)) {
            if (!Settings::enableSystemsMatchMakingNonegame()) {
                Session::flash('alert-danger', "Could not Enable the MatchMaking System Nonegame!");
                return Redirect::back();
            }
        } else {
            if (!Settings::disableSystemsMatchMakingNonegame()) {
                Session::flash('alert-danger', "Could not Disable the MatchMaking System Nonegame!");
                return Redirect::back();
            }
        }

        if (
            isset($request->maxopenperuser) && !Settings::setSystemsMatchMakingMaxopenperuser($request->maxopenperuser)
        ) {
            Session::flash('alert-danger', 'Could not update maxopenperuser!');
            return Redirect::back();
        }


        if (
            (isset($request->shop_status) && !Settings::setShopStatus($request->shop_status))
            ||
            (isset($request->shop_welcome_message) && !Settings::setShopWelcomeMessage($request->shop_welcome_message))
            ||
            (isset($request->shop_closed_message) && !Settings::setShopClosedMessage($request->shop_closed_message))
        ) {
            Session::flash('alert-danger', 'Could not update Shop settings fully!');
            return Redirect::back();
        }





        if (
            (isset($request->tournament_participation) &&
                !Settings::setCreditTournamentParticipation($request->tournament_participation)
            ) || (isset($request->tournament_first) &&
                !Settings::setCreditTournamentFirst($request->tournament_first)
            ) || (isset($request->tournament_second) &&
                !Settings::setCreditTournamentSecond($request->tournament_second)
            ) || (isset($request->tournament_third) &&
                !Settings::setCreditTournamentThird($request->tournament_third)
            ) || (isset($request->registration_event) &&
                !Settings::setCreditRegistrationEvent($request->registration_event)
            ) || (isset($request->registration_site) &&
                !Settings::setCreditRegistrationSite($request->registration_site)
            )
        ) {
            Session::flash('alert-danger', 'Could not apply credit system settings. Please try again.');
            return Redirect::back();
        }

        Session::flash('alert-success', "Successfully Saved OptSystems Settings!");
        return Redirect::back();
    }

    /**
     * Update Settings
     * @param  Request $request
     * @return Redirect
     */
    public function update(Request $request)
    {
        $rules = [
            'terms_and_conditions'      => 'filled',
            'org_name'                  => 'filled',
            'org_tagline'               => 'filled',
            'about_main'                => 'filled',
            'about_short'               => 'filled',
            'about_our_aim'             => 'filled',
            'about_who'                 => 'filled',
            'legal_notice'              => 'filled',
            'privacy_policy'            => 'filled',
            'seo_keywords'              => 'filled',
            'currency'                  => 'in:GBP,USD,EUR,SEK,DKK',
            'participant_count_offset'  => 'numeric',
            'event_count_offset'        => 'numeric',
            'org_logo'                  => 'image:allow_svg',
            'org_favicon'               => 'image:allow_svg',
            'site_locale'               => ['nullable', new ValidLocale]
        ];

        $messages = [
            'terms_and_conditions.filled'       => 'Terms And Conditions cannot be empty',
            'org_name.filled'                   => 'Org Name cannot be empty',
            'org_tagline.filled'                => 'Org Tagline cannot be empty',
            'about_main.filled'                 => 'About Main cannot be empty',
            'about_short.filled'                => 'About Short cannot be empty',
            'about_our_aim.filled'              => 'About Our Aim cannot be empty',
            'about_who.filled'                  => 'About Whos who cannot be empty',
            'legal_notice.filled'               => 'LegalNotice is required in Germany',
            'privacy_policy.filled'             => 'PrivacyPolicy is required in Germany',
            'seo_keywords.filled'               => 'SEO Keywords cannot be empty',
            'currency.in'                       => 'Currency must be GBP, USD, SEK, DKK or EUR',
            'participant_count_offset.numeric'  => 'Participant Count Offset must be a number',
            'event_count_offset.numeric'        => 'Lan Count Offset must be a number',
            'org_logo.image'                    => 'Org Logo must be a Image',
            'org_favicon'                       => 'Org Favicon must be a Image',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->steam_link) && !Settings::setSteamLink($request->steam_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->teamspeak_link) && !Settings::setTeamspeakLink($request->teamspeak_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->mumble_link) && !Settings::setMumbleLink($request->mumble_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->discord_link) && !Settings::setDiscordLink($request->discord_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->discord_id) && !Settings::setDiscordId($request->discord_id)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->reddit_link) && !Settings::setRedditLink($request->reddit_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->twitter_link) && !Settings::setTwitterLink($request->twitter_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->facebook_link) && !Settings::setFacebookLink($request->facebook_link)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (
            isset($request->participant_count_offset) &&
            !Settings::setParticipantCountOffset($request->participant_count_offset)
        ) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->event_count_offset) && !Settings::setEventCountOffset($request->event_count_offset)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->about_main) && !Settings::setAboutMain($request->about_main)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->frontpage_alot_tagline) && !Settings::setFrontpageAlotTagline($request->frontpage_alot_tagline)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->about_short) && !Settings::setAboutShort($request->about_short)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->about_our_aim) && !Settings::setAboutOurAim($request->about_our_aim)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->about_who) && !Settings::setAboutWho($request->about_who)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->legal_notice) && !Settings::setLegalNotice($request->legal_notice)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->privacy_policy) && !Settings::setPrivacyPolicy($request->privacy_policy)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (
            isset($request->purchase_terms_and_conditions) &&
            !Settings::setPurchaseTermsAndConditions($request->purchase_terms_and_conditions)
        ) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (
            isset($request->registration_terms_and_conditions) &&
            !Settings::setRegistrationTermsAndConditions($request->registration_terms_and_conditions)
        ) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->currency) && !Settings::setCurrency($request->currency)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->org_name) && !Settings::setOrgName($request->org_name)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->org_tagline) && !Settings::setOrgTagline($request->org_tagline)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->seo_keywords) && !Settings::setSeoKeywords($request->seo_keywords)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if ($request->file('org_logo') && !Settings::setOrgLogo($request->file('org_logo'))) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if ($request->file('org_favicon') && !Settings::setOrgFavicon($request->file('org_favicon'))) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        if (isset($request->site_locale) && !Settings::setSiteLocale($request->site_locale)) {
            Session::flash('alert-danger', 'Could not update!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated!');
        return Redirect::back();
    }

    /**
     * Enable Payment Gateway
     * @param  String $gateway
     * @return Redirect
     */
    public function enablePaymentGateway($gateway)
    {
        if (!Settings::enablePaymentGateway($gateway)) {
            Session::flash('alert-danger', "Could not Enable {$gateway}!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled {$gateway}!");
        return Redirect::back();
    }

    /**
     * Disable Payment Gateway
     * @param  String $gateway
     * @return Redirect
     */
    public function disablePaymentGateway($gateway)
    {
        if (!Settings::disablePaymentGateway($gateway)) {
            Session::flash('alert-danger', "Could not Disable {$gateway}!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled {$gateway}!");
        return Redirect::back();
    }

    /**
     * Enable Credit System
     * @return Redirect
     */
    public function enableCreditSystem()
    {
        if (!Settings::enableCreditSystem()) {
            Session::flash('alert-danger', "Could not Enable the Credit System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled the Credit System!");
        return Redirect::back();
    }

    /**
     * Disable Credit System
     * @return Redirect
     */
    public function disableCreditSystem()
    {
        if (!Settings::disableCreditSystem()) {
            Session::flash('alert-danger', "Could not Disable the Credit System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled the Credit System!");
        return Redirect::back();
    }

    /**
     * Enable Shop System
     * @return Redirect
     */
    public function enableShopSystem()
    {
        if (!Settings::enableShopSystem()) {
            Session::flash('alert-danger', "Could not Enable the Shop System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled the Shop System!");
        return Redirect::back();
    }

    /**
     * Disable Shop System
     * @return Redirect
     */
    public function disableShopSystem()
    {
        if (!Settings::disableShopSystem()) {
            Session::flash('alert-danger', "Could not Disable the Shop System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled the Shop System!");
        return Redirect::back();
    }

    /**
     * Enable Gallery System
     * @return Redirect
     */
    public function enableGallerySystem()
    {
        if (!Settings::enableGallerySystem()) {
            Session::flash('alert-danger', "Could not Enable the Gallery System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled the Gallery System!");
        return Redirect::back();
    }

    /**
     * Disable Gallery System
     * @return Redirect
     */
    public function disableGallerySystem()
    {
        if (!Settings::disableGallerySystem()) {
            Session::flash('alert-danger', "Could not Disable the Gallery System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled the Gallery System!");
        return Redirect::back();
    }

    /**
     * Enable Help System
     * @return Redirect
     */
    public function enableHelpSystem()
    {
        if (!Settings::enableHelpSystem()) {
            Session::flash('alert-danger', "Could not Enable the Help System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled the Help System!");
        return Redirect::back();
    }

    /**
     * Disable Help System
     * @return Redirect
     */
    public function disableHelpSystem()
    {
        if (!Settings::disableHelpSystem()) {
            Session::flash('alert-danger', "Could not Disable the Help System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled the Help System!");
        return Redirect::back();
    }

    /**
     * Enable MatchMaking System
     * @return Redirect
     */
    public function enableMatchMakingSystem()
    {
        if (!Settings::enableMatchMakingSystem()) {
            Session::flash('alert-danger', "Could not Enable the MatchMaking System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled the MatchMaking System!");
        return Redirect::back();
    }

    /**
     * Disable MatchMaking System
     * @return Redirect
     */
    public function disableMatchMakingSystem()
    {
        if (!Settings::disableMatchMakingSystem()) {
            Session::flash('alert-danger', "Could not Disable the MatchMaking System!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled the MatchMaking System!");
        return Redirect::back();
    }

    /**
     * Enable UserLocale
     * @return Redirect
     */
    public function enableUserLocale()
    {
        if (!Settings::enableUserLocale()) {
            Session::flash('alert-danger', "Could not Enable UserLocale!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled UserLocale!");
        return Redirect::back();
    }

    /**
     * Disable UserLocale
     * @return Redirect
     */
    public function disableUserLocale()
    {
        if (!Settings::disableUserLocale()) {
            Session::flash('alert-danger', "Could not Disable UserLocale!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled UserLocale!");
        return Redirect::back();
    }


    /**
     * Resets UserLocale of all users to current Site Locale
     * @return Redirect
     */
    public function resetUserLocales()
    {
        $count = 0;
        foreach (User::all() as $user) {
            $user->locale = Settings::getSiteLocale();
            $user->save();
            $count++;
        }
        Session::flash('alert-success', 'Successfully resetted userlocale to ' . Settings::getSiteLocale() . ' on ' . $count . ' Users!');
        return Redirect::back();
    }


    /**
     * Enable Login Method
     * @param  String $gateway
     * @return Redirect
     */
    public function enableLoginMethod($method)
    {
        if ($method == "steam" &&  Str::of(ApiKey::where('key', 'steam_api_key')->first()->value)->trim()->isEmpty()) {
            Session::flash('alert-danger', "Could not Enable {$method} because of missing api key!");
            return Redirect::back();
        }
        if (!Settings::enableLoginMethod($method)) {
            Session::flash('alert-danger', "Could not Enable {$method}!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Enabled {$method}!");
        return Redirect::back();
    }

    /**
     * Disable Login Method
     * @param  String $gateway
     * @return Redirect
     */
    public function disableLoginMethod($method)
    {
        if (count(Settings::getLoginMethods()) <= 1) {
            Session::flash('alert-danger', "You must have at least one Login Method enabled!");
            return Redirect::back();
        }


        if (!Settings::disableLoginMethod($method)) {
            Session::flash('alert-danger', "Could not Disable {$method}!");
            return Redirect::back();
        }
        Session::flash('alert-success', "Successfully Disabled {$method}!");
        return Redirect::back();
    }

    /**
     * Regenerate QR codes for Event Participants
     * @return Redirect
     */
    public function regenerateQRCodes()
    {
        $count = 0;
        foreach (Event::all() as $event) {
            if (!$event->eventParticipants->isEmpty()) {
                foreach ($event->eventParticipants as $participant) {
                    //DEBUG - Delete old images
                    $participant->generateQRCode();
                    $participant->save();
                    $count++;
                }
            }
        }
        Session::flash('alert-success', 'Successfully regenerated ' . $count . ' QR Codes!');
        return Redirect::back();
    }

    /**
     * Regenerate QR codes for Event Participants with a new QR Code URL
     * @return Redirect
     */
    public function regenerateQRCodesWithNewNames()
    {
        $count = 0;
        foreach (Event::all() as $event) {
            if (!$event->eventParticipants->isEmpty()) {
                foreach ($event->eventParticipants as $participant) {
                    $participant->generateQRCode(true);
                    $participant->save();
                    $count++;
                }
            }
        }
        Session::flash('alert-success', 'Successfully regenerated ' . $count . ' QR Codes with new names!');
        return Redirect::back();
    }

    /**
     * updateAuthGeneral
     * @param  Request $request
     * @return Redirect
     */
    public function updateAuthGeneral(Request $request)
    {

        $rules = [
            'auth_allow_email_change'               => 'in:on,off',
            'auth_require_phonenumber'               => 'in:on,off',
        ];
        $messages = [
            'auth_allow_email_change.in'                    => 'Public use must be true or false',
            'auth_require_phonenumber.in'                    => 'Require Phonenumber must be true or false',
        ];

        $this->validate($request, $rules, $messages);

        if ($request->auth_allow_email_change) {
            if (!Settings::enableAuthAllowEmailChange()) {
                Session::flash('alert-danger', "Could not Enable auth_allow_email_change!");
                return Redirect::back();
            }
        } else {
            if (!Settings::disableAuthAllowEmailChange()) {
                Session::flash('alert-danger', "Could not Disable auth_allow_email_change!");
                return Redirect::back();
            }
        }

        if ($request->auth_require_phonenumber) {
            if (!Settings::enableAuthRequirePhonenumber()) {
                Session::flash('alert-danger', "Could not Enable auth_require_phonenumber!");
                return Redirect::back();
            }
        } else {
            if (!Settings::disableAuthRequirePhonenumber()) {
                Session::flash('alert-danger', "Could not Disable auth_require_phonenumber!");
                return Redirect::back();
            }
        }

        Session::flash('alert-success', "Successfully Saved General Authentication Settings!");
        return Redirect::back();
    }
    /**
     * updateAuthSteam
     * @param  Request $request
     * @return Redirect
     */
    public function updateAuthSteam(Request $request)
    {

        $rules = [
            'auth_steam_require_email'               => 'in:on,off',

        ];
        $messages = [
            'auth_steam_require_email.in'                    => 'steam require email must be true or false',

        ];
        $this->validate($request, $rules, $messages);

        if ($request->auth_steam_require_email) {
            if (!Settings::enableAuthSteamRequireEmail()) {
                Session::flash('alert-danger', "Could not Enable auth_steam_require_email!");
                return Redirect::back();
            }
        } else {
            if (!Settings::disableAuthSteamRequireEmail()) {
                Session::flash('alert-danger', "Could not Disable auth_steam_require_email!");
                return Redirect::back();
            }
        }


        Session::flash('alert-success', "Successfully Saved General Authentication Settings!");
        return Redirect::back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;
use Helpers;
use FacebookPageWrapper as Facebook;
use \Carbon\Carbon as Carbon;

use App\User;
use App\Event;
use App\ShopOrder;
use App\Poll;
use App\PollOptionVote;
use App\EventParticipant;
use App\EventTournament;
use App\NewsComment;
use App\EventTicket;
use App\EventTournamentParticipant;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show Admin Index Page
     * @return view
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::all();
        $events = Event::all();
        $orders = ShopOrder::getNewOrders('login');
        $participants = EventParticipant::getNewParticipants('login');
        $participantCount = EventParticipant::all()->count();
        $tournamentCount = EventTournament::all()->count();
        $tournamentParticipantCount = EventTournamentParticipant::all()->count();
        $votes = PollOptionVote::getNewVotes('login');
        $comments = NewsComment::getNewComments('login');
        $tickets = EventTicket::all();
        $activePolls = Poll::where('end', '==', null)->orWhereBetween('end', ['0000-00-00 00:00:00', date("Y-m-d H:i:s")]);
        $facebookCallback = null;
        if (Facebook::isEnabled() && !Facebook::isLinked()) {
            $facebookCallback = Facebook::getLoginUrl();
        }
        $userLastLoggedIn = User::where('id', '!=', Auth::id())->latest('last_login')->first();
        $loginSupportedGateways = Settings::getSupportedLoginMethods();
        foreach ($loginSupportedGateways as $gateway) {
            $count = 0;
            switch ($gateway) {
                case 'steam':
                    $count = $users->where('steamid', '!=', null)->count();
                    break;
                default:
                    $count = $users->where('password', '!=', null)->count();
                    break;
            }
            $userLoginMethodCount[$gateway] = $count;
        }
        $orderBreakdown = array();
        foreach (ShopOrder::where('created_at', '>=', Carbon::now()->subMonths(12)->month)->get() as $order) {
            $orderBreakdown[date_format($order->created_at, 'm')][] = $order;
        }
        $ticketBreakdown = array();
        foreach (EventParticipant::where('created_at', '>=', Carbon::now()->subMonths(12)->month)->get() as $participant) {
            $ticketBreakdown[date_format($participant->created_at, 'm')][] = $participant;
        }
        return view('admin.index')
            ->with('user', $user)
            ->with('events', $events)
            ->with('orders', $orders)
            ->with('participants', $participants)
            ->with('votes', $votes)
            ->with('comments', $comments)
            ->with('tickets', $tickets)
            ->with('activePolls', $activePolls)
            ->with('shopEnabled', Settings::isShopEnabled())
            ->with('galleryEnabled', Settings::isGalleryEnabled())
            ->with('helpEnabled', Settings::isHelpEnabled())
            ->with('creditEnabled', Settings::isCreditEnabled())
            ->with('supportedLoginMethods', Settings::getSupportedLoginMethods())
            ->with('activeLoginMethods', Settings::getLoginMethods())
            ->with('supportedPaymentGateways', Settings::getSupportedPaymentGateways())
            ->with('activePaymentGateways', Settings::getPaymentGateways())
            ->with('facebookCallback', $facebookCallback)
            ->with('userLastLoggedIn', $userLastLoggedIn)
            ->with('userCount', $users->count())
            ->with('userLoginMethodCount', $userLoginMethodCount)
            ->with('participantCount', $participantCount)
            ->with('nextEvent', Helpers::getNextEventName())
            ->with('tournamentCount', $tournamentCount)
            ->with('tournamentParticipantCount', $tournamentParticipantCount)
            ->with('orderBreakdown', $orderBreakdown)
            ->with('ticketBreakdown', $ticketBreakdown);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;
use Helpers;
use \Carbon\Carbon as Carbon;

use App\User;
use App\Event;
use App\ShopOrder;
use App\Poll;
use App\PollOptionVote;
use App\Ticket;
use App\EventTournament;
use App\NewsComment;
use App\TicketType;
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
        //TODO Refactor View to not use Participant
        $user = Auth::user();
        $users = User::all();
        $events = Event::all();
        $orders = ShopOrder::getNewOrders('login');
        $participants = Ticket::getNewParticipants('login');
        $participantCount = Ticket::all()->count();
        $tournamentCount = EventTournament::all()->count();
        $tournamentParticipantCount = EventTournamentParticipant::all()->count();
        $votes = PollOptionVote::getNewVotes('login');
        $comments = NewsComment::getNewComments('login');
        $tickets = TicketType::all();
        $activePolls = Poll::where('end', '==', null)->orWhereBetween('end', ['0000-00-00 00:00:00', date("Y-m-d H:i:s")])->get();
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
        $orderBreakdown = collect(range(1, 12))
        ->mapWithKeys(function ($month) {
            return [Carbon::now()->startOfYear()->addMonthsNoOverflow($month - 1)->format('F') => 0];
        })
        ->merge(
            ShopOrder::where('created_at', '>=', Carbon::now()->subMonths(12))
                ->get()
                ->groupBy(function ($order) {
                    return Carbon::parse($order->created_at)->format('F');
                })
                ->map->count()
        )
        ->all();
        $ticketBreakdown = collect(range(0, 11))
        ->mapWithKeys(function ($month) {
            return [Carbon::now()->startOfYear()->addMonthsNoOverflow($month)->format('F') => 0];
        })
        ->merge(
            Ticket::where('created_at', '>=', Carbon::now()->subMonths(12))
                ->get()
                ->groupBy(function ($participant) {
                    return Carbon::parse($participant->created_at)->format('F');
                })
                ->map->count()
        )
        ->all();

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

<?php

namespace App\Http\Controllers\Admin\Events;

use Session;
use Settings;


use App\User;
use App\Event;
use App\TicketType;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TicketTypeController extends Controller
{
    /**
     * Show Tickets Index Page
     * @param  Event  $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index(Event $event)
    {
        // Get only users with staff or free tickets for this event
        $users = User::all()->filter(function($user) use ($event) {
            return $user->getFreeTickets($event->id)->count() > 0 || $user->getStaffTickets($event->id)->count() > 0;
        });
        
        $systemtickets = $users->sortByDesc(function($user) use ($event) {
            return [
                $user->getFreeTickets($event->id)->count(),
                $user->getStaffTickets($event->id)->count()
            ];
        })->values();
        
        $totalFreeTickets = $systemtickets->sum(function($user) use ($event) {
            return $user->getFreeTickets($event->id)->count();
        });

        $totalStaffTickets = $systemtickets->sum(function($user) use ($event) {
            return $user->getStaffTickets($event->id)->count();
        });

        //$purchaseBreakDown = $event->tickets()->withCount('participants')->get()->map(function ($ticket) {
        $purchaseBreakDown = $event->ticketTypes()->withCount('tickets')->get()->map(function ($ticketType) {
            return [
                'name' => $ticketType->name,
                'count' => $ticketType->tickets_count,
            ];
        })->toArray();

        $incomeBreakDown = $event->ticketTypes()->withCount('tickets')->get()->map(function ($ticketType) {
            return [
                'name' => $ticketType->name,
                'income' => $ticketType->price * $ticketType->tickets_count,
            ];
        })->toArray();

        return view('admin.events.tickets.index')
            ->with('event', $event)
            ->with('totalFreeTickets', $totalFreeTickets)
            ->with('totalStaffTickets', $totalStaffTickets)
            ->with('users', $users)
            ->with('purchaseBreakdownData', $purchaseBreakDown)
            ->with('incomeBreakdownData', $incomeBreakDown)
            ->with('global_ticket_hide_policy', Settings::getGlobalTicketTypeHidePolicy());
    }

    /**
     * Show Tickets Page
     * @param  Event       $event
     * @param  TicketType $ticketType
     * @return View
     */
    public function show(Event $event, TicketType $ticketType)
    {
        return view('admin.events.tickets.show')
            ->with('event', $event)
            ->with('global_tickettype_hide_policy', Settings::getGlobalTicketTypeHidePolicy())
            ->with('event_tickettype_hide_policy', $event->tickettype_hide_policy)
            ->with('ticketType', $ticketType);
    }

    /**
     * Store Ticket to Database
     * @param  Request $request
     * @param  Event   $event
     * @return Redirect
     */
    public function store(Request $request, Event $event)
    {
        $rules = [
            'name'              => 'required',
            'price'             => 'required|numeric',
            'sale_start_date'   => 'date_format:m/d/Y',
            'sale_start_time'   => 'date_format:H:i',
            'sale_end_date'     => 'date_format:m/d/Y',
            'sale_end_time'     => 'date_format:H:i',
            'type'              => 'required',
            'seatable'          => 'boolean',
            'quantity'          => 'numeric',
            'no_tickets_per_user' => 'numeric',
        ];
        $messages = [
            'name.required'                 => 'A Ticket Name is required',
            'price.numeric'                 => 'Price must be a number',
            'price.required'                => 'A Price is required',
            'sale_start_date.date'          => 'Sale Start Date must be m/d/Y format',
            'sale_start_time.date_format'   => 'Sale Start Time must be H:i format',
            'sale_end_date.date'            => 'Sale End Date must be m/d/Y format',
            'sale_end_time.date_format'     => 'Sale End Time must be H:i format',
            'seatable.boolen'               => 'Seatable must be True/False',
            'quantity.numeric'              => 'Quantity must be a number',
        ];
        $this->validate($request, $rules, $messages);

        if ($request->sale_start_date != '' || $request->sale_start_time != '') {
            $saleStart = date(
                "Y-m-d H:i:s",
                strtotime(
                    $request->sale_start_date . $request->sale_start_time
                )
            );
        }

        if ($request->sale_end_date != '' || $request->sale_end_time != '') {
            $saleEnd = date(
                "Y-m-d H:i:s",
                strtotime(
                    $request->sale_end_date . $request->sale_end_time
                )
            );
        }

        $ticketType             = new TicketType();
        $ticketType->event_id   = $event->id;
        $ticketType->name       = $request->name;
        $ticketType->type       = $request->type;
        $ticketType->price      = $request->price;
        $ticketType->seatable   = ($request->seatable ? true : false);

        $ticketType->sale_start = @$saleStart;
        $ticketType->sale_end   = @$saleEnd;
        $ticketType->quantity   = @$request->quantity;
        $ticketType->no_tickets_per_user = $request->no_tickets_per_user;
        $ticketType->event_ticket_group_id = empty($request->ticket_group) ? null : $request->ticket_group;

        if (!$ticketType->save()) {
            Session::flash('alert-danger', 'Cannot save Ticket Type');
            Redirect::back();
        }

        Session::flash('alert-success', 'Ticket saved Successfully');
        return Redirect::to('/admin/events/' . $event->slug . '/tickets/' . $ticketType->id);
    }

    /**
     * Update Ticket
     * @param  Request     $request
     * @param  Event       $event
     * @param  TicketType $ticketType
     * @return Redirect
     * TODO need to check if tickettype was already sold (has at least one ticket referenced. If so forbid to change or
     * build at least a warning + new logic to compensate pricing changes anywhere else)
     */
    public function update(Request $request, Event $event, TicketType $ticketType)
    {
        $rules = [
            'price'             => 'numeric',
            'name'              => 'filled',
            'sale_start_date'   => 'date',
            'sale_start_time'   => 'date_format:H:i',
            'sale_end_date'     => 'date',
            'sale_end_time'     => 'date_format:H:i',
            'seatable'          => 'boolean',
            'type'              => 'filled',
            'quantity'          => 'numeric',
            'no_tickets_per_user' => 'numeric',
            'tickettype_hide_policy' => 'integer|between:-1,15',
        ];
        $messages = [
            'price|numeric'                 => 'Price must be a number',
            'name|filled'                   => 'Name cannot be empty',
            'sale_start_date.date'          => 'Sale Start Date must be m/d/Y format',
            'sale_start_time.date_format'   => 'Sale Start Time must be H:i format',
            'sale_end_date.date'            => 'Sale End Date must be m/d/Y format',
            'sale_end_time.date_format'     => 'Sale End Time must be H:i format',
            'seatable|boolen'               => 'Seatable must be True/False',
            'quantity|numeric'              => 'Quantity must be a number',
            'tickettype_hide_policy|integer' => 'Ticket Type Hide Policy must be a number!',
            'tickettype_hide_policy|between' => 'Ticket Type Hide Policy must be a value between -1 and 15',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->price) && (!$ticketType->tickets->isEmpty() && $ticketType->price != $request->price)) {
            Session::flash('alert-danger', 'Cannot update Ticket price when tickets have been bought!');
            return Redirect::back();
        }

        if (isset($request->name)) {
            $ticketType->name = $request->name;
        }

        if (isset($request->type)) {
            $ticketType->type = $request->type;
        }

        if (isset($request->sale_start_date) || isset($request->sale_start_time)) {
            if ($request->sale_start_date != '' || $request->sale_start_time != '') {
                $saleStart = date(
                    "Y-m-d H:i:s",
                    strtotime(
                        $request->sale_start_date . $request->sale_start_time
                    )
                );
            }
        }

        if (isset($request->sale_end_date) || isset($request->sale_end_time)) {
            if ($request->sale_end_date != '' || $request->sale_end_time != '') {
                $saleEnd = date(
                    "Y-m-d H:i:s",
                    strtotime(
                        $request->sale_end_date . $request->sale_end_time
                    )
                );
            }
        }

        $ticketType->sale_start = @$saleStart;
        $ticketType->sale_end   = @$saleEnd;
        if (isset($request->price)) {
            $ticketType->price = $request->price;
        }

        if (isset($request->quantity)) {
            $ticketType->quantity   = $request->quantity;
        }

        if (isset($request->no_tickets_per_user)) {
            $ticketType->no_tickets_per_user = $request->no_tickets_per_user;
        }

        if (isset($request->tickettype_hide_policy)) {
            $ticketType->tickettype_hide_policy = $request->tickettype_hide_policy;
        }

        $ticketType->seatable   = ($request->seatable ? true : false);
        $ticketType->event_ticket_group_id = empty($request->ticket_group) ? null : $request->ticket_group;

        if (!$ticketType->save()) {
            Session::flash('alert-danger', 'Cannot update Ticket!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Ticket updated Successfully!');
        return Redirect::back();
    }

    /**
     * Delete Ticket from Database
     * @param  Event       $event
     * @param  TicketType $ticketType
     * @return redirect
     */
    public function destroy(Event $event, TicketType $ticketType)
    {
        if ($ticketType->participants && $ticketType->participants()->count() > 0) {
            Session::flash('alert-danger', 'Cannot delete Ticket, Purchases have been made!');
            return Redirect::back();
        }

        if (!$ticketType->delete()) {
            Session::flash('alert-danger', 'Cannot delete Ticket!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Ticket!');
        return Redirect::to('admin/events/' . $event->slug . '/tickets');
    }
}

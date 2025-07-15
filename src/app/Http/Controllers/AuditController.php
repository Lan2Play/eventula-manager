<?php

namespace App\Http\Controllers;

use App\Ticket;
use Auth;
use Illuminate\View\View;


class AuditController extends Controller
{
    /**
     * Shows the audit Logs for a Ticket
     * @param Ticket $ticket
     * @return View
     */
    public function showAuditsForTickets(Ticket $ticket): View
    {
        $user = Auth::user();

        if (! (
            $user->is($ticket->user) ||
            $user->is($ticket->manager) ||
            $user->getAdmin()
        )) {
            abort(403);
        }

        if ($user->is($ticket->owner) || $user->getAdmin()) {
            // Owner oder Admin sieht den kompletten Log
            $audits = $ticket->audits()->get();
        } else {
            // Manager sieht nur Einträge, in denen seine ID als manager_id geloggt wurde
            $audits = $ticket->audits()
                ->where('old_values->manager_id', $user->id)
                ->orWhere('new_values->manager_id', $user->id)
                ->get();
        }

        return view('audit.ticket_audit_log', compact('ticket', 'audits'));
    }
}
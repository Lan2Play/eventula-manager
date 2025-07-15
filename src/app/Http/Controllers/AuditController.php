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
            $user->is($ticket->owner) ||
            $user->is($ticket->manager) ||
            $user->getAdmin()
        )) {
            abort(403);
        }

        if ($user->is($ticket->owner) || $user->getAdmin()) {
            // Owner oder Admin sieht alles
            $audits = $ticket->audits()->get();
        } else {
            // Manager/User sieht nur Einträge, in denen seine ID bei user_id oder manager_id beteiligt ist
            $all      = $ticket->audits()->get();
            $visibleIds = $ticket->audits()
                ->where(function($q) use ($user) {
                    $q->where('old_values->manager_id', $user->id)
                        ->orWhere('new_values->manager_id', $user->id)
                        ->orWhere('old_values->user_id',    $user->id)
                        ->orWhere('new_values->user_id',    $user->id);
                })
                ->pluck('id')
                ->toArray();

            $audits = $all->map(function($audit) use ($visibleIds) {
                if (! in_array($audit->id, $visibleIds)) {
                    $audit->redacted = true;
                }
                return $audit;
            });
        }

        return view('audit.ticket_audit_log', compact('ticket', 'audits'));
    }
}
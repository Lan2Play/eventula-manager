<?php

namespace App\Http\Controllers\Admin\Events;


use Session;

use App\Event;
use App\EventAnnouncement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AnnouncementsController extends Controller
{
    /**
     * Add Announcement to Datbase
     * @param  Request $request
     * @param  Event   $event
     * @return Redirect
     */
    public function store(Request $request, Event $event)
    {
        $rules = [
            'message'           => 'required'
        ];
        $messages = [
            'message.required'  => 'Some Text is required',
        ];
        $this->validate($request, $rules, $messages);

        $announcement           = new EventAnnouncement();
        $announcement->message  = $request->message;
        $announcement->event_id = $event->id;
        
        if (!$announcement->save()) {
            Session::flash('alert-danger', 'Cannot save Announcement!');
            return Redirect::to('admin/events/' . $event->slug);
        }

        Session::flash('alert-success', 'Successfully saved Announcement!');
        return Redirect::to('admin/events/' . $event->slug);
    }

    /**
     * Update Announcement
     * @param  Request          $request
     * @param  Event            $event
     * @param  EventAnnouncement $announcement
     * @return Redirect
     */
    public function update(Request $request, Event $event, EventAnnouncement $announcement)
    {
        $rules = [
            'message'           => 'required'
        ];
        $messages = [
            'message.required'  => 'Some Text is required',
        ];
        $this->validate($request, $rules, $messages);

        $announcement->message  = $request->message;
        
        if (!$announcement->save()) {
            Session::flash('alert-danger', 'Cannot update Announcement!');
            return Redirect::to('admin/events/' . $event->slug);
        }

        Session::flash('message', 'Successfully updated Announcement!');
        return Redirect::to('admin/events/' . $event->slug);
    }

    /**
     * Delete Announcement from Database
     * @param  Event            $event
     * @param  EventAnnouncement $announcement
     * @return Redirect
     */
    public function destroy(Event $event, EventAnnouncement $announcement)
    {
        if (!$announcement->delete()) {
            Session::flash('alert-danger', 'Cannot delete Announcement!');
            return Redirect::to('admin/events/' . $event->slug);
        }

        session::flash('message', 'Successfully deleted Announcement!');
        return Redirect::to('admin/events/' . $event->slug);
    }
}

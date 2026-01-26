<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'max_ticket_event_count_reached' => 'You can not buy :ticketname x:ticketamount because it would exceed the event\'s limit of :maxamount total ticket(s) per user. You currently have :currentamount total ticket(s) for this event.',
    'max_ticket_group_count_reached' => 'You can not buy :ticketname x:ticketamount, because it would exceed the limit of :maxamount ticket(s) from the group :ticketgroup per user. You currently have :currentamount ticket(s).',
    'max_ticket_type_count_reached' => 'You can not buy :ticketname x:ticketamount, because it would exceed the limit of :maxamount ticket(s) per user. You currently have :currentamount ticket(s).',

    
    /* Ticket Partial*/
    'has_been_gifted' => 'This Ticket has been gifted!',
    'not_eligable_for_seat' => 'This Ticket is not eligable for a seat',
    'has_been_revoked' => 'This ticket has been revoked!',
    'gift_ticket' => 'Gift Ticket', 
    'gift_url' => 'Gift URL:',
    'revoke_gift' => 'Revoke Gift',
    'staff_ticket' => 'Staff Ticket',
    'free_ticket' => 'Free Ticket',
    'ticket_type' => 'Ticket Type',
    'change_user' => 'Change User',
    'change_manager' => 'Change Manager',
    'change_seat_tooltip' => 'Only the ticket user or manager can change seating',
    'tooltip_ticket_role_owner' => 'Always keeps full control over the ticket',
    'tooltip_ticket_role_manager' => 'Can change the User of a ticket and the seat the ticket is using',
    'tooltip_ticket_role_user' => '\'Uses\' the ticket to get entrance to the event, the ticket user will be able to use the ticket',
    'modal_change_manager_search_manager_label' => 'Search for new Manager',
    'modal_change_manager_headline' => 'What can a Manager do?',
    'modal_change_manager_text' => 'A Manager can change the User of a ticket and the seat the ticket is using.',
    'modal_change_manager_example' => 'Example: A Clan Member buys tickets for his buddy and himself. They both are members of a clan. The Clan Manager will be the one managing the clan\'s visit to an event, therefore he can change the seats a ticket will occupy and the user that is using the ticket.',
    'modal_change_user_search_user_label' => 'Search for new User',
    'modal_change_user_headline' => 'What can a User do?',
    'modal_change_user_text' => 'A User "uses" the Ticket to get entrance to the event, seating, tournaments and basically everything that is related to the event.',
    'modal_change_user_example' => 'A user that is a user of a ticket sees the ticket in their profile and on the event page but does not have access to the controls of the ticket (changing manager or user) but could also change the seat.',
    'owner_cant_be_changed' => 'Owner can not be changed',
    'only_owner_can_chang_manager' => 'Only the owner can change the manager',
    'only_owner_or_manager_can_change_user' => 'Only the ticket owner or manager can change the user',
    'buttons_save' => 'save',
    'buttons_close' => 'close',
    'ticket_id' => 'Ticket ID',
    'select_seat' => 'Select Seat',
    'no_owner' => 'None',
    'no_manager' => 'None',
    'no_user' => 'None',
    'signed_in' => 'Checked in',

    /* Alerts */
    'alert_event_not_yet_published' => 'The event is currently in the state :state. You can not buy tickets for for now.',
    'alert_event_not_found' => 'Event Not found',
    'alert_user_not_found' => 'User not found',
    'alert_ticket_not_found' => 'Ticket not found',
    'alert_event_ended' => 'You cannot buy tickets for previous events',
    'alert_ticket_not_yet' => 'You cannot buy this ticket yet.',
    'alert_ticket_not_anymore' => 'You cannot buy this ticket anymore.',

    /* Auditing */
    'manager' => 'Manager',
    'owner' => 'Owner',
    'user' => 'User',
    'either_staff_or_free' => 'Is either staff or free ticket',
    'current_ticket_details' => 'Current ticket details',
    'ticket_roles' => 'Ticket Roles',
    'audit_log' => 'Audit Log',


    /* Ticket PDF */
    'pdf_header' => 'Your ticket for :name',
    'ticket_name' => 'Ticket name',
    'seat' => 'Seat',
    'seat_in' => 'Seated in',
    'username' => 'Username',
    'realname' => 'Name',
    'realname_format' => ':firstname :lastname',
    'present_qr_code' => 'Please present this QR code at the entry gate.',
    'generated_at' => 'This document has been created on :date at :time',

    /* Ticket PDF views */
    'not_allowed' => 'You are not allowed to view this ticket',
    'wrong_file_format' => 'Unsupported file type',
    'download_pdf' => 'Download PDF',
];

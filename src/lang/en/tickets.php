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
    'ticket_not_yet' => 'You cannot buy this ticket yet.',
    'ticket_not_anymore' => 'You cannot buy this ticket anymore.',
    
    /* Ticket Partial*/
    'has_been_gifted' => 'This Ticket has been gifted!',
    'not_eligable_for_seat' => 'This Ticket is not eligable for a seat',
    'gift_ticket' => 'Gift Ticket', 
    'gift_url' => 'Gift URL:',
    'revoke_gift' => 'Revoke Gift',
    'staff_ticket' => 'Staff Ticket',
    'free_ticket' => 'Free Ticket',

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

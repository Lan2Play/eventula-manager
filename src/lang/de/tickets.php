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

    'max_ticket_event_count_reached' => 'Du kannst :ticketname nicht :ticketamount mal kaufen, da sonst das Limit von insgesamt :maxamount Ticket(s) für das Event überschritten würde. Du hast aktuell insgesamt :currentamount Ticket(s) für das Event.',
    'max_ticket_group_count_reached' => 'Du kannst :ticketname nicht :ticketamount Mal kaufen, da sonst das Limit von :maxamount Ticket(s) aus der Gruppe :ticketgroup pro Nutzer überschritten würde. Du hast aktuell :currentamount Ticket(s).',
    'max_ticket_type_count_reached' => 'Du kannst :ticketname nicht :ticketamount Mal kaufen, da sonst das Limit von :maxamount Ticket(s) pro Nutzer überschritten würde. Du hast aktuell :currentamount Ticket(s).',
    'ticket_not_yet' => 'Das Ticket kann noch nicht gekauft werden.',
    'ticket_not_anymore' => 'Das Ticket kann nicht mehr gekauft werden.',

    /* Ticket Partial*/
    'has_been_gifted' => 'Dieses Ticket wurde verschenkt!',
    'not_eligable_for_seat' => 'Mit diesem Ticket kann kein Sitzplatz gebucht werden',
    'has_been_revoked' => 'Dieses Ticket wurde zurückgerufen!',
    'gift_ticket' => 'Verschenke Ticket', 
    'gift_url' => 'Geschenk URL:',
    'revoke_gift' => 'Geschenk zurückziehen',
    'staff_ticket' => 'Team Ticket',
    'free_ticket' => 'Kostenloses Ticket',
    'signed_in' => 'Eingecheckt',
    'owner_cant_be_changed' => "Besitzer kann nicht geändert werden",

    /* Auditing */
    'manager' => 'Verwalter',
    'owner' => 'Besitzer',
    'user' => 'Benutzer',
    'either_staff_or_free' => 'Ist entweder Team oder Kostenloses Ticket',
    'current_ticket_details' => 'Aktuelle Ticket Details',
    'select_seat' => 'Platz auswählen',
    'no_owner' => 'Keiner',
    'no_manager' => 'Keiner',
    'no_user' => 'Keiner',
    'alert_ticket_not_found' => 'Ticket nicht gefunden',

    /* Ticket PDF */
    'pdf_header' => 'Dein Ticket für :name',
    'ticket_name' => 'Ticket Name',
    'seat' => 'Sitzplatz',
    'seat_in' => 'Platziert in',
    'username' => 'Username',
    'realname' => 'Name',
    'realname_format' => ':firstname :lastname',
    'present_qr_code' => 'Bitte zeige den QR Code am Einlass vor.',
    'generated_at' => 'Dieses Dokument wurde erstellt am :date um :time',

    /* Ticket PDF views */
    'not_allowed' => 'Du bist nicht berechtigt dieses Ticket anzusehen',
    'wrong_file_format' => 'Nicht unterstützter Dateityp',
    'download_pdf' => 'PDF herunterladen',
];

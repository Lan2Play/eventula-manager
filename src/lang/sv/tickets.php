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

    'max_ticket_count_reached' => 'Du kan inte köpa denna biljett längre. Gränsen för antal biljetter (:maxticketcount st) har nåtts.',
    'ticket_not_yet' => 'Du kan inte köpa denna biljett ännu',
    'ticket_not_anymore' => 'Du kan inte köpa denna biljett längre',

    /* Ticket Partial*/
    'has_been_gifted' => 'Denna biljett har getts bort!',
    'not_eligable_for_seat' => 'Denna biljett har ingen platsreservation',
    'gift_ticket' => 'Ge bort biljett',
    'gift_url' => 'Gåvo-URL:',
    'revoke_gift' => 'Dra tillbaka gåva',
    'staff_ticket' => 'Personalbiljett',
    'free_ticket' => 'Gratisbiljett',

    /* Ticket PDF */
    'pdf_header' => 'Din biljett för :name',
    'ticket_name' => 'Biljettnamn',
    'seat' => 'Plats',
    'seat_in' => 'Satt på',
    'username' => 'Användarnamn',
    'realname' => 'Namn',
    'realname_format' => ':firstname :lastname',
    'present_qr_code' => 'Visa denna QR-kod vid entrén.',
    'generated_at' => 'Detta dokument skapades :date vid :time',

    /* Ticket PDF views */
    'not_allowed' => 'Du har ingen tillåtelse att se biljetten',
    'wrong_file_format' => 'Okänd filtyp',
    'download_pdf' => 'Ladda ner PDF',
];

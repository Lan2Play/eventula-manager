<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Error Pages
    |--------------------------------------------------------------------------
    |
    |
    */
    '400' => [
        'header' => 'Hoppsan - 400 - Felaktig begäran',
        'message' => 'Begäran kunde inte förstås av servern.',
    ],
    '401' => [
        'header' => 'Hoppsan - 401 - Obehörig',
        'message' => 'Autentisering krävs för att komma åt denna resurs.',
    ],
    '403' => [
        'header' => 'Hoppsan - 403 - Förbjuden',
        'message' => 'Du har inte tillåtelse att komma åt denna resurs.',
    ],
    '404' => [
        'header' => 'Hoppsan - 404 - Hittades inte',
        'message' => 'Vi kunde inte hitta sidan du letade efter.',
    ],
    '500' => [
        'header' => 'Hoppsan - 500 - Internt serverfel',
        'message' => 'Något gick fel på våra servrar.',
    ],
    '502' => [
        'header' => 'Hoppsan - 502 - Felaktig gateway',
        'message' => 'Servern fick ett ogiltigt svar från uppströmsservern.',
    ],
    '503' => [
        'header' => 'Hoppsan - 503 - Tjänsten otillgänglig',
        'message' => 'Strax tillbaka.',
    ],
];

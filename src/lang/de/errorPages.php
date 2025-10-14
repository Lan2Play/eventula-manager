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
        'header' => 'Hoppla - 400 - Ungültige Anfrage',
        'message' => 'Die Anfrage konnte vom Server nicht verstanden werden.',
    ],
    '401' => [
        'header' => 'Hoppla - 401 - Nicht autorisiert',
        'message' => 'Authentifizierung ist erforderlich, um auf diese Ressource zuzugreifen.',
    ],
    '403' => [
        'header' => 'Hoppla - 403 - Verboten',
        'message' => 'Sie sind nicht berechtigt, auf diese Ressource zuzugreifen.',
    ],
    '404' => [
        'header' => 'Hoppla - 404 - Nicht gefunden',
        'message' => 'Wir konnten die Seite, die Sie gesucht haben, nicht finden.',
    ],
    '500' => [
        'header' => 'Hoppla - 500 - Interner Serverfehler',
        'message' => 'Auf unseren Servern ist ein Fehler aufgetreten.',
    ],
    '502' => [
        'header' => 'Hoppla - 502 - Ungültiges Gateway',
        'message' => 'Der Server hat eine ungültige Antwort vom Upstream-Server erhalten.',
    ],
    '503' => [
        'header' => 'Hoppla - 503 - Dienst nicht verfügbar',
        'message' => 'Sind gleich wieder da.',
    ],
];

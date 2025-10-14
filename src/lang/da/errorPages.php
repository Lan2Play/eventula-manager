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
        'header' => 'Ups - 400 - Forkert anmodning',
        'message' => 'Anmodningen kunne ikke forstås af serveren.',
    ],
    '401' => [
        'header' => 'Ups - 401 - Uautoriseret',
        'message' => 'Godkendelse er påkrævet for at få adgang til denne ressource.',
    ],
    '403' => [
        'header' => 'Ups - 403 - Forbudt',
        'message' => 'Du har ikke tilladelse til at få adgang til denne ressource.',
    ],
    '404' => [
        'header' => 'Ups - 404 - Ikke fundet',
        'message' => 'Vi kunne ikke finde den side, du ledte efter.',
    ],
    '500' => [
        'header' => 'Ups - 500 - Intern serverfejl',
        'message' => 'Noget gik galt på vores servere.',
    ],
    '502' => [
        'header' => 'Ups - 502 - Forkert gateway',
        'message' => 'Serveren modtog et ugyldigt svar fra upstream-serveren.',
    ],
    '503' => [
        'header' => 'Ups - 503 - Tjeneste utilgængelig',
        'message' => 'Er straks tilbage.',
    ],
];

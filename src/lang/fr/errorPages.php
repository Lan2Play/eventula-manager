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
        'header' => 'Oups - 400 - Mauvaise requête',
        'message' => 'La requête n\'a pas pu être comprise par le serveur.',
    ],
    '401' => [
        'header' => 'Oups - 401 - Non autorisé',
        'message' => 'Une authentification est requise pour accéder à cette ressource.',
    ],
    '403' => [
        'header' => 'Oups - 403 - Interdit',
        'message' => 'Vous n\'êtes pas autorisé à accéder à cette ressource.',
    ],
    '404' => [
        'header' => 'Oups - 404 - Non trouvé',
        'message' => 'Nous n\'avons pas pu trouver la page que vous recherchiez.',
    ],
    '500' => [
        'header' => 'Oups - 500 - Erreur interne du serveur',
        'message' => 'Quelque chose s\'est mal passé sur nos serveurs.',
    ],
    '502' => [
        'header' => 'Oups - 502 - Mauvaise passerelle',
        'message' => 'Le serveur a reçu une réponse invalide du serveur en amont.',
    ],
    '503' => [
        'header' => 'Oups - 503 - Service indisponible',
        'message' => 'De retour bientôt.',
    ],
];

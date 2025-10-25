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
        'header' => 'Oops - 400 - Bad Request',
        'message' => 'The request could not be understood by the server.',
    ],
    '401' => [
        'header' => 'Oops - 401 - Unauthorized',
        'message' => 'Authentication is required to access this resource.',
    ],
    '403' => [
        'header' => 'Oops - 403 - Forbidden',
        'message' => 'You are not allowed to access this resource.',
    ],
    '404' => [
        'header' => 'Oops - 404 - Not Found',
        'message' => 'We couldn\'t find the page you were looking for.',
    ],
    '500' => [
        'header' => 'Oops - 500 - Internal Server Error',
        'message' => 'Something went wrong on our servers.',
    ],
    '502' => [
        'header' => 'Oops - 502 - Bad Gateway',
        'message' => 'The server received an invalid response from the upstream server.',
    ],
    '503' => [
        'header' => 'Oops - 503 - Service Unavailable',
        'message' => 'Be right back.',
    ],
];
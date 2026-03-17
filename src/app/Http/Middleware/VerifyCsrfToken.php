<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    public function __construct(
        \Illuminate\Contracts\Foundation\Application $app,
        \Illuminate\Contracts\Encryption\Encrypter $encrypter
    ) {
        parent::__construct($app, $encrypter);

        // Populated here because config() cannot be used in property initializers.
        if ($path = config('plausible.event_path')) {
            $this->except[] = $path;
        }
    }
}

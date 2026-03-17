<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PlausibleProxyController extends Controller
{
    /**
     * Serve the Plausible tracking script, cached for 24 hours.
     */
    public function script(): Response
    {
        if (!config('plausible.enabled')) {
            return response('', 204);
        }

        $scriptUrl = config('plausible.script_url');

        $script = Cache::remember('plausible-script', now()->addHours(24), function () use ($scriptUrl) {
            $response = Http::get($scriptUrl);

            return $response->successful() ? $response->body() : '';
        });

        return response($script, 200, [
            'Content-Type'  => 'application/javascript',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Forward a Plausible event to the Plausible API.
     */
    public function event(Request $request): Response
    {
        if (!config('plausible.enabled')) {
            return response('', 202);
        }

        $apiUrl = config('plausible.api_url', 'https://plausible.io/api/event');

        $response = Http::withHeaders([
            'User-Agent'     => $request->userAgent(),
            'X-Forwarded-For' => $request->ip(),
            'Content-Type'   => 'text/plain',
        ])->withBody($request->getContent(), 'text/plain')
            ->post($apiUrl);

        return response($response->body(), $response->status());
    }
}

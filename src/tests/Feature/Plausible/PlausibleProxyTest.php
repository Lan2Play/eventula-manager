<?php

namespace Tests\Feature\Plausible;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature tests for the Plausible Analytics proxy routes.
 *
 * External HTTP calls to Plausible are always faked with Http::fake() — no
 * real network access is required. Tests cover:
 *
 *  - GET  /js/script.js  → serves / caches the tracking script
 *  - POST /api/event     → proxies events to Plausible with correct headers
 *  - Both endpoints return early (204 / 202) when Plausible is disabled
 */
class PlausibleProxyTest extends TestCase
{
    use RefreshDatabase;

    private const FAKE_SCRIPT = 'window.plausible=function(){};';
    private const SCRIPT_URL  = 'https://plausible.io/js/pa-TEST.js';
    private const API_URL     = 'https://plausible.io/api/event';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();

        // Start each test with Plausible disabled and a clean cache.
        config([
            'plausible.enabled'    => false,
            'plausible.script_url' => self::SCRIPT_URL,
            'plausible.api_url'    => self::API_URL,
        ]);

        Cache::forget('plausible-script');
    }

    // =========================================================================
    // GET /js/script.js
    // =========================================================================

    #[Test]
    public function script_endpoint_returns_204_when_plausible_disabled(): void
    {
        Http::fake(); // no HTTP calls should be made

        $this->get('/js/script.js')->assertNoContent();

        Http::assertNothingSent();
    }

    #[Test]
    public function script_endpoint_returns_javascript_when_enabled(): void
    {
        Http::fake([self::SCRIPT_URL => Http::response(self::FAKE_SCRIPT, 200)]);

        config(['plausible.enabled' => true]);

        $this->get('/js/script.js')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript')
            ->assertHeader('Cache-Control', 'max-age=86400, public')
            ->assertSee(self::FAKE_SCRIPT, escape: false);
    }

    #[Test]
    public function script_is_fetched_from_upstream_only_once_and_then_served_from_cache(): void
    {
        Http::fake([self::SCRIPT_URL => Http::response(self::FAKE_SCRIPT, 200)]);

        config(['plausible.enabled' => true]);

        $this->get('/js/script.js')->assertOk();
        $this->get('/js/script.js')->assertOk();

        // Upstream must only have been called once despite two requests.
        Http::assertSentCount(1);
    }

    #[Test]
    public function script_endpoint_returns_empty_body_when_upstream_fails(): void
    {
        Http::fake([self::SCRIPT_URL => Http::response('', 500)]);

        config(['plausible.enabled' => true]);

        $response = $this->get('/js/script.js');

        $response->assertOk();
        $this->assertSame('', $response->getContent());
    }

    // =========================================================================
    // POST /api/event
    // =========================================================================

    #[Test]
    public function event_endpoint_returns_202_when_plausible_disabled(): void
    {
        Http::fake();

        $this->postJson('/api/event', ['name' => 'pageview', 'url' => 'http://localhost/'])
            ->assertStatus(202);

        Http::assertNothingSent();
    }

    #[Test]
    public function event_endpoint_forwards_body_to_plausible_api(): void
    {
        Http::fake([self::API_URL => Http::response('', 202)]);

        config(['plausible.enabled' => true]);

        $payload = json_encode(['name' => 'pageview', 'url' => 'http://localhost/']);

        $this->call(
            'POST',
            '/api/event',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain', 'HTTP_USER_AGENT' => 'TestBrowser/1.0'],
            $payload,
        )->assertStatus(202);

        Http::assertSent(function ($request) use ($payload) {
            return $request->url() === self::API_URL
                && $request->body() === $payload;
        });
    }

    #[Test]
    public function event_endpoint_forwards_user_agent_and_x_forwarded_for_headers(): void
    {
        Http::fake([self::API_URL => Http::response('', 202)]);

        config(['plausible.enabled' => true]);

        $this->call(
            'POST',
            '/api/event',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'     => 'text/plain',
                'HTTP_USER_AGENT'  => 'Mozilla/5.0 (Test)',
                'REMOTE_ADDR'      => '1.2.3.4',
            ],
            '{"name":"pageview","url":"http://localhost/"}',
        );

        Http::assertSent(function ($request) {
            return $request->hasHeader('User-Agent', 'Mozilla/5.0 (Test)')
                && $request->hasHeader('X-Forwarded-For', '1.2.3.4');
        });
    }

    #[Test]
    public function event_endpoint_passes_upstream_status_code_back_to_client(): void
    {
        Http::fake([self::API_URL => Http::response('Bad Request', 400)]);

        config(['plausible.enabled' => true]);

        $this->call('POST', '/api/event', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(400);
    }

    #[Test]
    public function event_endpoint_does_not_require_csrf_token(): void
    {
        Http::fake([self::API_URL => Http::response('', 202)]);

        config(['plausible.enabled' => true]);

        // withoutMiddleware is NOT used — CSRF exemption in VerifyCsrfToken must cover the route.
        $this->call('POST', '/api/event', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(202);
    }

    // =========================================================================
    // Config: ENV kill-switch behaviour
    // =========================================================================

    #[Test]
    public function plausible_enabled_config_false_suppresses_script(): void
    {
        Http::fake();

        config(['plausible.enabled' => false]);

        $this->get('/js/script.js')->assertNoContent();
        Http::assertNothingSent();
    }

    #[Test]
    public function plausible_enabled_config_true_activates_script(): void
    {
        Http::fake([self::SCRIPT_URL => Http::response(self::FAKE_SCRIPT, 200)]);

        config(['plausible.enabled' => true]);

        $this->get('/js/script.js')->assertOk();
    }

    // =========================================================================
    // Self-hosted Plausible: custom api_url is honoured
    // =========================================================================

    #[Test]
    public function event_is_forwarded_to_custom_self_hosted_api_url(): void
    {
        $selfHostedUrl = 'https://plausible.myhost.de/api/event';

        Http::fake([$selfHostedUrl => Http::response('', 202)]);

        config([
            'plausible.enabled' => true,
            'plausible.api_url' => $selfHostedUrl,
        ]);

        $this->call('POST', '/api/event', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(202);

        Http::assertSent(fn ($r) => $r->url() === $selfHostedUrl);
        Http::assertNotSent(fn ($r) => $r->url() === self::API_URL);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class AnalyticsHeadTest extends TestCase
{
    protected function renderHead(string $url): string
    {
        $this->app->instance('request', Request::create($url));
        config(['analytics.enabled' => true, 'analytics.measurement_id' => 'G-ZMQ2EZKSBP']);

        return view('partials.analytics-head')->render();
    }

    public function test_production_uses_one_google_tag_and_no_gtm_container(): void
    {
        $html = $this->renderHead('https://levelupgamehub.com/topup');
        $this->assertSame(1, substr_count($html, 'gtag/js?id=G-ZMQ2EZKSBP'));
        $this->assertStringNotContainsString('gtm.js', $html);
        $this->assertStringNotContainsString('GTM-NC65L328', $html);
        $this->assertStringContainsString('location.origin + location.pathname', $html);
    }

    public function test_localhost_and_unapproved_hosts_never_load_remote_trackers(): void
    {
        foreach (['http://127.0.0.1:8000/', 'https://staging.example.com/', 'https://levelupgamehub.com/track-order'] as $url) {
            $html = $this->renderHead($url);
            $this->assertStringNotContainsString('gtag/js', $html);
            $this->assertStringNotContainsString('connect.facebook.net', $html);
            $this->assertStringNotContainsString('clarity.ms/tag', $html);
        }
    }

    public function test_form_flash_does_not_expose_contact_details_or_unapproved_event_names(): void
    {
        session()->flash('analytics_event', 'private@example.com');
        session()->flash('success', 'Private sender +62123456789');
        $html = $this->renderHead('https://levelupgamehub.com/contact');
        $this->assertStringNotContainsString('private@example.com', $html);
        $this->assertStringNotContainsString('+62123456789', $html);
        $this->assertStringContainsString('successEvent', $html);
    }
}

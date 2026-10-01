<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ShortLink;
use App\Models\TrackingEvent;
use App\Models\VisitorSession;
use App\Services\Analytics\BotDetector;
use App\Services\Analytics\OrderAttributor;
use App\Services\Analytics\VisitorTracker;
use App\Services\Google\Ga4Client;
use App\Services\Google\ServiceAccountToken;
use App\Services\Saweria\OrderRecorder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
 * Angka panel pernah membengkak puluhan kali lipat karena pemantau uptime dan
 * perayap tanpa cookie terhitung sebagai pengunjung. Test di sini menjaga agar
 * kebocoran yang sama tidak terulang.
 */
class AnalyticsAccuracyTest extends TestCase
{
    use RefreshDatabase;

    // Basis data lokal tidak boleh ikut dikosongkan oleh RefreshDatabase
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    public function test_uptime_monitors_and_meta_crawler_are_recognised_as_bots(): void
    {
        $detector = new BotDetector();

        $this->assertSame([true, 'Uptime Kuma'], $detector->inspect($this->request('/', 'Uptime-Kuma/2.5.3')));
        $this->assertSame([true, 'Meta'], $detector->inspect($this->request('/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 (compatible; meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler))')));
        $this->assertSame([false, null], $detector->inspect($this->request('/', $this->browser())));
    }

    public function test_checkout_click_without_cookie_is_flagged_as_bot(): void
    {
        $tracker = app(VisitorTracker::class);
        $tracker->record($this->request('/ke-checkout/free-fire', $this->browser()), TrackingEvent::CHECKOUT_CLICK);

        $session = VisitorSession::sole();
        $this->assertTrue($session->is_bot);
        $this->assertSame('tanpa cookie', $session->bot_name);
        $this->assertSame(0, VisitorSession::humans()->count());
    }

    public function test_checkout_click_with_cookie_stays_on_the_human_session(): void
    {
        $id = (string) Str::uuid();

        app(VisitorTracker::class)->record($this->request('/topup/free-fire', $this->browser(), $id), TrackingEvent::PAGE_VIEW);
        // Instance baru, seperti permintaan HTTP berikutnya
        app()->forgetInstance(VisitorTracker::class);
        app(VisitorTracker::class)->record($this->request('/ke-checkout/free-fire', $this->browser(), $id), TrackingEvent::CHECKOUT_CLICK);

        $session = VisitorSession::sole();
        $this->assertSame($id, $session->id);
        $this->assertFalse($session->is_bot);
        $this->assertSame(1, $session->checkout_clicks);
    }

    public function test_first_visit_to_a_product_page_is_one_session(): void
    {
        // Meniru halaman produk: controller mencatat product_view, lalu
        // middleware TrackVisitor mencatat page_view pada permintaan yang sama.
        Route::middleware('web')->get('/__produk-uji', function (Request $request, VisitorTracker $tracker) {
            $tracker->record($request, TrackingEvent::PRODUCT_VIEW, ['item_slug' => 'free-fire']);

            return response('<html></html>', 200, ['Content-Type' => 'text/html']);
        });

        $response = $this->withHeader('User-Agent', $this->browser())->get('/__produk-uji')->assertOk();

        $session = VisitorSession::sole();
        $response->assertCookie(VisitorTracker::COOKIE, $session->id);
        $this->assertSame(1, $session->page_views);
        $this->assertSame(1, $session->product_views);
    }

    public function test_short_link_hands_its_session_to_the_landing_page(): void
    {
        ShortLink::create(['code' => 'promo-uji', 'label' => 'Promo uji', 'template' => 'custom', 'target' => 'https://levelupgamehub.com/topup', 'active' => true]);

        $response = $this->withHeader('User-Agent', $this->browser())->get('/s/promo-uji')->assertRedirect();

        $response->assertCookie(VisitorTracker::COOKIE, VisitorSession::sole()->id);
    }

    public function test_saweria_timestamps_are_stored_in_app_timezone(): void
    {
        $order = app(OrderRecorder::class)->record([
            'id' => (string) Str::uuid(),
            'amount_raw' => 16304,
            'created_at' => '2026-09-18T22:46:54.204442+07:00',
        ]);

        $this->assertSame('2026-09-18 15:46:54', $order->fresh()->ordered_at->format('Y-m-d H:i:s'));
    }

    public function test_attribution_matches_human_click_and_ignores_bot_clicks(): void
    {
        $human = $this->visitor(false);
        $bot = $this->visitor(true);

        $this->click($human, '54 Diamonds (50 + 4 Bonus)', '2026-09-18 15:46:27');
        // Klik bot lebih dekat waktunya, tetapi tetap tidak boleh dipilih
        $this->click($bot, '54 Diamonds (50 + 4 Bonus)', '2026-09-18 15:46:50');

        $order = Order::create([
            'saweria_id' => (string) Str::uuid(),
            'state' => 'completed',
            'product_name' => '54 Diamonds (50 + 4 Bonus)',
            'ordered_at' => Carbon::parse('2026-09-18 15:46:54'),
        ]);

        $this->assertSame($human->id, app(OrderAttributor::class)->attribute($order)?->id);
        $this->assertSame('tinggi', $order->fresh()->attribution);
    }

    public function test_ga4_summary_reads_the_single_total_row(): void
    {
        config(['google.ga4.property_id' => '123']);
        $this->mock(ServiceAccountToken::class)->shouldReceive('accessToken')->andReturn('token');

        Http::fake(['analyticsdata.googleapis.com/*' => Http::response([
            'rows' => [['metricValues' => array_map(fn ($v) => ['value' => $v], ['18', '17', '34', '61', '95.5', '0.75', '0.25'])]],
        ])]);

        $summary = app(Ga4Client::class)->summary(14);

        $this->assertSame(34, $summary['sessions']);
        $this->assertSame(18, $summary['users']);
        $this->assertSame(61, $summary['page_views']);
    }

    public function test_health_check_is_light_and_untracked(): void
    {
        $this->withHeader('User-Agent', $this->browser())
            ->get('/up')
            ->assertOk()
            ->assertSee('ok')
            ->assertCookieMissing(VisitorTracker::COOKIE)
            ->assertCookieMissing(config('session.cookie'));

        $this->assertSame(0, VisitorSession::count());
    }

    protected function request(string $path, string $agent, ?string $cookie = null): Request
    {
        return Request::create($path, 'GET', [], $cookie ? [VisitorTracker::COOKIE => $cookie] : [], [], [
            'HTTP_USER_AGENT' => $agent,
        ]);
    }

    protected function browser(): string
    {
        return 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    }

    protected function visitor(bool $bot): VisitorSession
    {
        return VisitorSession::create([
            'id' => (string) Str::uuid(),
            'is_bot' => $bot,
            'started_at' => Carbon::parse('2026-09-18 15:40:00'),
            'last_seen_at' => Carbon::parse('2026-09-18 15:40:00'),
        ]);
    }

    protected function click(VisitorSession $session, string $product, string $at): void
    {
        TrackingEvent::create([
            'session_id' => $session->id,
            'name' => TrackingEvent::CHECKOUT_CLICK,
            'path' => '/ke-checkout/mobile-legends-bang-bang',
            'meta' => ['product_name' => $product],
            'occurred_at' => Carbon::parse($at),
        ]);
    }
}

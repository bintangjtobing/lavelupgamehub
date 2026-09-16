<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    private const TRACK_ID = '11111111-2222-4333-8444-555555555555';

    protected function setUp(): void
    {
        parent::setUp();

        config(['saweria.base_url' => 'https://saweria.test']);
        Http::preventStrayRequests();
    }

    public function test_get_renders_an_empty_tracking_form(): void
    {
        $this->withoutMiddleware([
            \Fahlisaputra\Minify\Middleware\MinifyCss::class,
            \Fahlisaputra\Minify\Middleware\MinifyJavascript::class,
            \Fahlisaputra\Minify\Middleware\MinifyHtml::class,
        ]);

        $this->get(route('orders.track'))
            ->assertOk()
            ->assertViewIs('pages.track-order')
            ->assertViewHas('order', null)
            ->assertViewHas('error', null)
            ->assertViewHas('trackId', '');

        Http::assertNothingSent();
    }

    public function test_successful_lookup_returns_only_normalized_display_fields(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder()])]);

        $response = $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID]);

        $response->assertOk()
            ->assertJsonPath('error', null)
            ->assertJsonPath('trackId', self::TRACK_ID)
            ->assertJsonPath('order.id', self::TRACK_ID)
            ->assertJsonPath('order.game_name', 'Mobile Legends')
            ->assertJsonPath('order.product_name', 'Weekly Pass')
            ->assertJsonPath('order.cover', 'https://cdn.test/cover.jpg')
            ->assertJsonPath('order.payment_method', 'QRIS')
            ->assertJsonPath('order.payment_status', 'Pembayaran berhasil')
            ->assertJsonPath('order.fulfillment_status', 'Pesanan selesai')
            ->assertJsonPath('order.state', 'completed')
            ->assertJsonPath('order.step', 3)
            ->assertJsonPath('order.created_at', '16/09/2026 10:00 WIB')
            ->assertJsonPath('order.paid_at', '16/09/2026 10:05 WIB')
            ->assertJsonPath('order.product_price', 158384)
            ->assertJsonPath('order.fee', 1122)
            ->assertJsonPath('order.total', 159506)
            ->assertJsonPath('order.currency', 'IDR')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertSame([
            'id',
            'game_name',
            'product_name',
            'cover',
            'payment_method',
            'payment_status',
            'fulfillment_status',
            'step',
            'state',
            'created_at',
            'paid_at',
            'product_price',
            'fee',
            'total',
            'currency',
        ], array_keys($response->json('order')));

        foreach (['upstream-private-id', 'private-user', 'payment-secret', 'qr-secret', 'va-secret', 'account-secret', 'voucher-secret', '1569'] as $secret) {
            $this->assertStringNotContainsString($secret, $response->getContent());
        }

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://saweria.test/game-vouchers/tracking-order/'.self::TRACK_ID;
        });
        Http::assertSentCount(1);
    }

    public function test_payment_success_does_not_complete_a_pending_fulfillment(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'status' => 'PENDING',
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertOk()
            ->assertJsonPath('order.payment_status', 'Pembayaran berhasil')
            ->assertJsonPath('order.fulfillment_status', 'Menunggu diproses')
            ->assertJsonPath('order.state', 'processing')
            ->assertJsonPath('order.step', 1);
    }

    public function test_an_unknown_fulfillment_status_remains_neutral_even_when_paid(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'status' => 'NEW_UNDOCUMENTED_STATUS',
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertOk()
            ->assertJsonPath('order.fulfillment_status', 'Status pemrosesan belum diketahui')
            ->assertJsonPath('order.state', 'unknown')
            ->assertJsonPath('order.step', 1);
    }

    public function test_payment_failure_expiry_and_refund_override_fulfillment_success(): void
    {
        $paymentStatuses = ['FAILED', 'EXPIRED', 'REFUNDED'];

        Http::fake(function (Request $request) use (&$paymentStatuses) {
            $paymentStatus = array_shift($paymentStatuses);

            return Http::response(['data' => $this->successfulOrder([
                'payment_status' => $paymentStatus,
            ])]);
        });

        foreach ([
            ['failed', 'Pembayaran gagal'],
            ['expired', 'Pembayaran kedaluwarsa'],
            ['refunded', 'Pembayaran dikembalikan'],
        ] as [$state, $paymentLabel]) {
            $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
                ->assertOk()
                ->assertJsonPath('order.state', $state)
                ->assertJsonPath('order.payment_status', $paymentLabel)
                ->assertJsonPath('order.fulfillment_status', 'Pesanan selesai')
                ->assertJsonPath('order.step', 0);
        }
    }

    public function test_fulfillment_success_with_pending_payment_is_not_completed(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'payment_status' => 'PENDING',
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertOk()
            ->assertJsonPath('order.state', 'unknown')
            ->assertJsonPath('order.step', 0);
    }

    public function test_payment_timestamp_is_omitted_until_payment_succeeds(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'status' => 'PENDING',
            'payment_status' => 'PENDING',
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertOk()
            ->assertJsonPath('order.state', 'pending')
            ->assertJsonPath('order.step', 0)
            ->assertJsonPath('order.paid_at', null);
    }

    public function test_invalid_track_id_returns_422_without_an_upstream_request(): void
    {
        Http::fake();

        $this->postJson(route('orders.lookup'), ['track_id' => '<script>bad()</script>'])
            ->assertStatus(422)
            ->assertJsonPath('order', null)
            ->assertJsonPath('trackId', '<script>bad()</script>')
            ->assertJsonPath('error', 'Track ID harus berupa UUID yang valid.')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        Http::assertNothingSent();
    }

    public function test_non_string_track_id_returns_422_without_a_warning_or_upstream_request(): void
    {
        Http::fake();

        $this->postJson(route('orders.lookup'), ['track_id' => ['unexpected']])
            ->assertStatus(422)
            ->assertJsonPath('order', null)
            ->assertJsonPath('trackId', '')
            ->assertJsonPath('error', 'Track ID tidak valid.');

        Http::assertNothingSent();
    }

    public function test_missing_order_returns_404_without_exposing_upstream_data(): void
    {
        Http::fake(['*' => Http::response(['message' => 'not found'], 404)]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertNotFound()
            ->assertJsonPath('order', null)
            ->assertJsonPath('error', 'Pesanan tidak ditemukan. Periksa kembali Track ID.')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_upstream_http_connection_and_malformed_failures_return_503(): void
    {
        Http::fakeSequence()
            ->push(['message' => 'unavailable'], 503)
            ->push(['data' => 'invalid'], 200)
            ->push(['data' => $this->successfulOrder(['currency' => 'not-valid'])], 200);

        foreach (range(1, 3) as $_) {
            $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
                ->assertStatus(503)
                ->assertJsonPath('order', null)
                ->assertJsonPath('error', 'Status pesanan sedang tidak tersedia. Coba lagi sebentar.')
                ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }

        Http::assertSentCount(3);

        Http::fake(fn () => throw new ConnectionException('connection failed'));

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertStatus(503)
            ->assertJsonPath('order', null)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_mismatched_upstream_order_id_is_rejected(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'id' => '99999999-8888-4777-8666-555555555555',
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertStatus(503)
            ->assertJsonPath('order', null)
            ->assertJsonPath('error', 'Status pesanan sedang tidak tersedia. Coba lagi sebentar.');
    }

    public function test_unsafe_cover_and_unknown_payment_method_are_not_reflected(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'etc' => array_merge($this->successfulOrder()['etc'], ['payment_method' => '<script>method()</script>']),
            'game_voucher' => array_merge($this->successfulOrder()['game_voucher'], [
                'thumbnail' => 'https://user:password@cdn.test/cover.jpg',
            ]),
        ])])]);

        $response = $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID]);

        $response->assertOk()
            ->assertJsonPath('order.cover', null)
            ->assertJsonPath('order.payment_method', 'Metode pembayaran lainnya');
        $this->assertStringNotContainsString('password', $response->getContent());
        $this->assertStringNotContainsString('<script>', $response->getContent());
    }

    public function test_valid_alternative_currency_is_preserved_and_untrusted_fee_is_omitted(): void
    {
        Http::fake(['*' => Http::response(['data' => $this->successfulOrder([
            'currency' => 'usd',
            'vendor_cut' => 9999,
        ])])]);

        $this->postJson(route('orders.lookup'), ['track_id' => self::TRACK_ID])
            ->assertOk()
            ->assertJsonPath('order.currency', 'USD')
            ->assertJsonPath('order.fee', null)
            ->assertJsonPath('order.total', 159506);
    }

    public function test_named_limiter_uses_ip_and_hashed_normalized_track_id_keys(): void
    {
        $limiter = RateLimiter::limiter('order-tracking');
        $request = HttpRequest::create('/track-order', 'POST', [
            'track_id' => '  '.strtoupper(self::TRACK_ID).'  ',
        ], server: ['REMOTE_ADDR' => '192.0.2.10']);

        $limits = $limiter($request);

        $this->assertSame([120, 20], array_map(fn ($limit) => $limit->maxAttempts, $limits));
        $this->assertStringContainsString(hash('sha256', self::TRACK_ID), $limits[1]->key);
        $this->assertStringNotContainsString(self::TRACK_ID, $limits[1]->key);
        $this->assertStringNotContainsString('192.0.2.10', $limits[0]->key);
        $this->assertContains('throttle:order-tracking', app('router')->getRoutes()
            ->getByName('orders.lookup')->gatherMiddleware());
    }

    protected function successfulOrder(array $overrides = []): array
    {
        return array_replace_recursive([
            'id' => self::TRACK_ID,
            'username' => 'private-user',
            'amount_raw' => 159506,
            'created_at' => '2026-09-16T03:00:00Z',
            'currency' => 'IDR',
            'expired_at' => '2026-09-16T04:00:00Z',
            'payment_status' => 'SUCCESS',
            'payment_updated_at' => '2026-09-16T03:05:00Z',
            'status' => 'SUCCESS',
            'vat' => 0,
            'vat_rate' => 0,
            'vendor_cut' => 1122,
            'etc' => [
                'amount_to_display' => 1569,
                'is_game_voucher' => true,
                'payment_method' => 'qris',
                'payment_url' => 'payment-secret',
                'qr_string' => 'qr-secret',
                'virtual_account_number' => 'va-secret',
            ],
            'game_voucher' => [
                'group_name' => 'Mobile Legends',
                'group_slug' => 'mobile-legends',
                'group_variant' => 'DIGITAL',
                'product_name' => 'Weekly Pass',
                'product_slug' => 'weekly-pass',
                'selling_price' => 158384,
                'thumbnail' => 'https://cdn.test/cover.jpg',
                'account_id' => 'account-secret',
                'voucher_code' => 'voucher-secret',
            ],
        ], $overrides);
    }
}

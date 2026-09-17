<?php

namespace App\Services\Analytics;

use App\Models\TrackingEvent;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/*
 * Pelacak kunjungan milik sendiri.
 *
 * Batasan yang disengaja:
 *  - alamat IP tidak pernah disimpan
 *  - user agent hanya dipakai untuk menyimpulkan jenis perangkat, lalu dibuang
 *  - pengenal sesi berupa UUID acak di cookie, bukan sidik jari perangkat
 *
 * Kegagalan pelacakan tidak boleh menjatuhkan halaman. Pemanggil membungkus
 * seluruh pemakaian kelas ini dengan try/catch.
 */
class VisitorTracker
{
    public function __construct(protected BotDetector $bots)
    {
    }

    public const COOKIE = 'lu_vid';

    protected const LIFETIME_MINUTES = 60 * 24 * 90; // 90 hari

    protected ?VisitorSession $session = null;

    public function sessionId(Request $request): ?string
    {
        $id = $request->cookie(self::COOKIE);

        return is_string($id) && Str::isUuid($id) ? $id : null;
    }

    /**
     * Ambil sesi yang sedang berjalan, atau buat baru bila belum ada.
     */
    public function resolve(Request $request): VisitorSession
    {
        if ($this->session) {
            return $this->session;
        }

        $id = $this->sessionId($request) ?: (string) Str::uuid();
        $now = Carbon::now();

        $session = VisitorSession::find($id);
        $bot = $this->bots->inspect($request);

        if ($session === null) {
            $session = VisitorSession::create([
                'id' => $id,
                // Sumber trafik diambil dari kunjungan pertama dan tidak ditimpa.
                'utm_source' => $this->param($request, 'utm_source'),
                'utm_medium' => $this->param($request, 'utm_medium'),
                'utm_campaign' => $this->param($request, 'utm_campaign'),
                'utm_content' => $this->param($request, 'utm_content'),
                'utm_term' => $this->param($request, 'utm_term'),
                'referrer_host' => $this->referrerHost($request),
                'landing_path' => $this->path($request),
                'device' => $this->device($request),
                'is_bot' => $bot[0],
                'bot_name' => $bot[1],
                'started_at' => $now,
                'last_seen_at' => $now,
            ]);
        } else {
            $session->last_seen_at = $now;

            // Kunjungan pertama bisa saja tanpa UTM, lalu pengunjung kembali
            // lewat tautan berkampanye. Dalam hal itu barulah UTM diisi.
            if (! $session->utm_source && $this->param($request, 'utm_source')) {
                $session->utm_source = $this->param($request, 'utm_source');
                $session->utm_medium = $this->param($request, 'utm_medium');
                $session->utm_campaign = $this->param($request, 'utm_campaign');
                $session->utm_content = $this->param($request, 'utm_content');
                $session->utm_term = $this->param($request, 'utm_term');
            }

            $session->save();
        }

        return $this->session = $session;
    }

    public function record(Request $request, string $name, array $attributes = []): ?TrackingEvent
    {
        $session = $this->resolve($request);

        $event = TrackingEvent::create(array_merge([
            'session_id' => $session->id,
            'name' => $name,
            'path' => $this->path($request),
            'occurred_at' => Carbon::now(),
        ], $attributes));

        $counter = [
            TrackingEvent::PAGE_VIEW => 'page_views',
            TrackingEvent::PRODUCT_VIEW => 'product_views',
            TrackingEvent::CHECKOUT_CLICK => 'checkout_clicks',
        ][$name] ?? null;

        if ($counter) {
            $session->increment($counter);
        }

        return $event;
    }

    public function cookieLifetime(): int
    {
        return self::LIFETIME_MINUTES;
    }

    /**
     * Jalur halaman yang selalu diawali satu garis miring, termasuk halaman depan.
     */
    protected function path(Request $request): string
    {
        return Str::limit('/'.ltrim($request->path(), '/'), 190, '');
    }

    protected function param(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        // Nilai kampanye yang wajar itu pendek dan tanpa karakter aneh. Menyaring
        // di sini mencegah laporan dikotori oleh URL yang dikarang orang lain.
        return preg_match('/^[\pL\pN _.\-\/]{1,80}$/u', $value) ? $value : null;
    }

    protected function referrerHost(Request $request): ?string
    {
        $referrer = $request->headers->get('referer');

        if (! is_string($referrer) || $referrer === '') {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        if (! is_string($host) || $host === '' || $host === $request->getHost()) {
            return null;
        }

        return Str::limit(ltrim($host, 'www.'), 190, '');
    }

    protected function device(Request $request): string
    {
        $agent = (string) $request->userAgent();

        if (preg_match('/iPad|Tablet/i', $agent)) {
            return 'tablet';
        }

        return preg_match('/Mobile|Android|iPhone/i', $agent) ? 'mobile' : 'desktop';
    }
}

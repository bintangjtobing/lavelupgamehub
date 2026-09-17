<?php

namespace App\Http\Middleware;

use App\Models\TrackingEvent;
use App\Services\Analytics\VisitorTracker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/*
 * Mencatat kunjungan halaman dan memasang cookie pengenal sesi.
 *
 * Pelacakan dijalankan SETELAH respons dibentuk, dan seluruhnya dibungkus
 * try/catch: kalau pencatatan gagal, halaman tetap harus tampil. Statistik
 * tidak pernah lebih penting daripada situsnya sendiri.
 */
class TrackVisitor
{
    public function __construct(protected VisitorTracker $tracker)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $session = $this->tracker->resolve($request);

            $this->tracker->record($request, TrackingEvent::PAGE_VIEW);

            if ($this->tracker->sessionId($request) === null) {
                Cookie::queue(Cookie::make(
                    VisitorTracker::COOKIE,
                    $session->id,
                    $this->tracker->cookieLifetime(),
                    null,
                    null,
                    $request->secure(),
                    true,   // httpOnly: cookie ini tidak perlu dibaca JavaScript
                    false,
                    'lax'
                ));
            }
        } catch (Throwable $e) {
            Log::warning('Pencatatan kunjungan gagal.', ['reason' => $e->getMessage()]);
        }

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax()) {
            return false;
        }

        // Hanya halaman yang benar-benar tampil yang dihitung
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        // Halaman admin adalah milik pengelola, bukan trafik pengunjung
        return ! $request->is('admin', 'admin/*', 'webhooks/*');
    }
}

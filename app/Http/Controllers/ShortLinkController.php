<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Models\TrackingEvent;
use App\Services\Analytics\BotDetector;
use App\Services\Analytics\VisitorTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Pembuka tautan pendek: /s/{kode}
 *
 * Pengalihan memakai 302, bukan 301. Peramban menyimpan 301 secara permanen,
 * sehingga tujuan tautan tidak bisa diubah lagi dan klik berikutnya tidak
 * pernah sampai ke sini untuk dihitung.
 */
class ShortLinkController extends Controller
{
    public function __invoke(
        Request $request,
        string $code,
        VisitorTracker $tracker,
        BotDetector $bots
    ) {
        $link = ShortLink::where('code', $code)->first();

        // Kode salah, sudah dinonaktifkan, atau kedaluwarsa: antar ke halaman
        // depan, jangan tampilkan halaman galat kepada calon pembeli.
        if ($link === null || ! $link->isUsable()) {
            return redirect()->to('/')->with('status', 'Tautan tidak berlaku lagi.');
        }

        $isBot = $bots->isBot($request);

        try {
            $link->registerClick($isBot);

            // Bot tidak ikut dicatat sebagai kunjungan, supaya pratinjau tautan
            // di WhatsApp atau Facebook tidak terhitung sebagai orang.
            if (! $isBot) {
                $tracker->record($request, TrackingEvent::PAGE_VIEW, [
                    'meta' => [
                        'short_link' => $link->code,
                        'template' => $link->template,
                    ],
                ]);
            }
        } catch (Throwable $e) {
            // Pencatatan gagal tidak boleh menghalangi orang membuka tautannya.
            Log::warning('Pencatatan klik tautan pendek gagal.', [
                'code' => $code,
                'reason' => $e->getMessage(),
            ]);
        }

        return redirect()->away($link->resolved_target, 302);
    }
}

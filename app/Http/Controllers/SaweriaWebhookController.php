<?php

namespace App\Http\Controllers;

use App\Services\Saweria\OrderRecorder;
use App\Services\Saweria\WebhookSignature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Penerima callback Saweria.
 *
 * Saweria mencatat kegagalan callback ("Hitungan Gagal" di halaman Integrasi),
 * jadi endpoint ini harus menjawab cepat dan hanya membalas selain 2xx bila
 * memang ada yang salah.
 */
class SaweriaWebhookController extends Controller
{
    public function __invoke(Request $request, WebhookSignature $signature, OrderRecorder $recorder): JsonResponse
    {
        // Tanpa stream key tidak ada cara membedakan callback asli dari kiriman
        // orang lain, jadi lebih baik menolak daripada menyimpan data yang tidak
        // bisa dipercaya. 503 membuat Saweria mencoba lagi setelah kunci diisi.
        if (! $signature->isConfigured()) {
            Log::error('Callback Saweria ditolak: SAWERIA_STREAM_KEY belum diisi.');

            return response()->json(['message' => 'Webhook belum dikonfigurasi.'], 503);
        }

        $payload = $request->json()->all();

        if (! is_array($payload) || ! is_string($payload['id'] ?? null) || trim($payload['id']) === '') {
            Log::warning('Callback Saweria tanpa id yang sah ditolak.');

            return response()->json(['message' => 'Payload tidak valid.'], 422);
        }

        if (! $signature->verify($payload, $request->header('Saweria-Callback-Signature'))) {
            Log::warning('Callback Saweria dengan tanda tangan tidak cocok ditolak.', [
                'saweria_id' => $payload['id'],
            ]);

            return response()->json(['message' => 'Tanda tangan tidak cocok.'], 401);
        }

        try {
            $order = $recorder->record($payload);
        } catch (Throwable $e) {
            // Balas 500 supaya Saweria mengirim ulang; pesanan jangan sampai hilang.
            Log::error('Pesanan Saweria gagal disimpan.', [
                'saweria_id' => $payload['id'],
                'reason' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Gagal menyimpan pesanan.'], 500);
        }

        // Pesanan sudah aman tersimpan. Percobaan melengkapi detail di bawah ini
        // sengaja tidak memengaruhi status balasan: kalau gagal, perintah
        // terjadwal saweria:sync-orders yang akan menuntaskannya.
        try {
            $recorder->enrich($order);
        } catch (Throwable $e) {
            Log::warning('Detail pesanan gagal dilengkapi saat callback.', [
                'saweria_id' => $order->saweria_id,
                'reason' => $e->getMessage(),
            ]);
        }

        return response()->json(['message' => 'ok'], 200);
    }
}

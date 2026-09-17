<?php

namespace App\Services\Analytics;

use App\Models\Order;
use App\Models\TrackingEvent;
use App\Models\VisitorSession;
use Illuminate\Support\Carbon;

/*
 * Menebak kunjungan mana yang menghasilkan sebuah pesanan.
 *
 * Ini memang tebakan, dan bentuk datanya memaksa begitu: pembayaran terjadi di
 * halaman Saweria, dan callback yang kembali tidak membawa penanda sesi kita.
 * Yang bisa dilakukan adalah mencocokkan pesanan dengan penekanan tombol
 * checkout terakhir untuk produk yang sama, dalam rentang waktu yang wajar.
 *
 * Karena itu setiap hasil diberi label tingkat keyakinan, dan laporan wajib
 * menampilkan label tersebut apa adanya:
 *
 *   tinggi - satu-satunya klik checkout untuk produk itu pada rentang waktunya
 *   sedang - ada beberapa kandidat, yang terdekat waktunya yang dipilih
 *   (null) - tidak ada kandidat; pesanan dianggap tanpa sumber
 */
class OrderAttributor
{
    protected const WINDOW_MINUTES = 90;

    public function attribute(Order $order): ?VisitorSession
    {
        if ($order->session_id || ! $order->product_name) {
            return null;
        }

        $orderedAt = $order->ordered_at ?: $order->created_at;

        if (! $orderedAt) {
            return null;
        }

        $candidates = TrackingEvent::where('name', TrackingEvent::CHECKOUT_CLICK)
            ->whereNotNull('session_id')
            ->whereBetween('occurred_at', [
                $orderedAt->copy()->subMinutes(self::WINDOW_MINUTES),
                $orderedAt->copy()->addMinutes(10),
            ])
            ->where('meta->product_name', $order->product_name)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        // Pengurutan dilakukan di PHP, bukan lewat SQL, supaya tidak bergantung
        // pada fungsi tanggal yang berbeda antara SQLite (lokal) dan MySQL (server).
        $chosen = $candidates
            ->sortBy(fn ($event) => abs($event->occurred_at->diffInSeconds($orderedAt)))
            ->first();
        $session = VisitorSession::find($chosen->session_id);

        if ($session === null) {
            return null;
        }

        $order->forceFill([
            'session_id' => $session->id,
            'attribution' => $candidates->count() === 1 ? 'tinggi' : 'sedang',
            'utm_source' => $session->utm_source,
            'utm_medium' => $session->utm_medium,
            'utm_campaign' => $session->utm_campaign,
        ])->save();

        return $session;
    }

    /**
     * Catat pesanan sebagai peristiwa funnel, supaya langkah terakhir ikut
     * terhitung bersama langkah-langkah sebelumnya.
     */
    public function recordFunnelEvents(Order $order): void
    {
        $this->touchEvent($order, TrackingEvent::ORDER_CREATED, $order->ordered_at ?: $order->created_at);

        if ($order->paid_at) {
            $this->touchEvent($order, TrackingEvent::ORDER_PAID, $order->paid_at);
        }
    }

    protected function touchEvent(Order $order, string $name, ?Carbon $at): void
    {
        TrackingEvent::updateOrCreate(
            ['name' => $name, 'meta->order_id' => $order->saweria_id],
            [
                'session_id' => $order->session_id,
                'item_slug' => null,
                'product_slug' => null,
                'value' => $order->amount_raw,
                'meta' => [
                    'order_id' => $order->saweria_id,
                    'product_name' => $order->product_name,
                    'game_name' => $order->game_name,
                ],
                'occurred_at' => $at ?: Carbon::now(),
            ]
        );
    }
}

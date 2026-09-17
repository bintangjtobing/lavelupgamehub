<?php

namespace App\Services\Analytics;

use App\Models\Order;
use App\Models\TrackingEvent;
use App\Models\VisitorSession;
use Illuminate\Support\Carbon;

/*
 * Menyusun angka untuk halaman analitik.
 *
 * Funnel dihitung per SESI, bukan per peristiwa. Satu orang yang membuka lima
 * produk tetap dihitung satu, supaya tingkat konversinya menjawab "berapa
 * banyak pengunjung yang sampai ke langkah ini", bukan "berapa kali diklik".
 */
class FunnelReport
{
    public function __construct(protected Carbon $since, protected Carbon $until)
    {
    }

    public static function forDays(int $days): self
    {
        return new self(
            Carbon::now()->subDays(max(1, $days))->startOfDay(),
            Carbon::now()->endOfDay()
        );
    }

    /**
     * Langkah funnel beserta jumlah sesi dan tingkat lanjutnya.
     */
    public function funnel(): array
    {
        $sessions = $this->sessionsInRange();
        $total = $sessions->count();

        $reached = [];
        foreach ([TrackingEvent::PRODUCT_VIEW, TrackingEvent::CHECKOUT_CLICK] as $step) {
            $reached[$step] = TrackingEvent::where('name', $step)
                ->whereIn('session_id', $sessions)
                ->distinct()
                ->count('session_id');
        }

        $orders = $this->ordersInRange();
        $ordersWithSession = (clone $orders)->whereNotNull('session_id')->distinct()->count('session_id');
        $paidWithSession = (clone $orders)->whereNotNull('session_id')
            ->whereIn('state', ['completed', 'processing'])
            ->distinct()->count('session_id');

        $rows = [
            ['key' => TrackingEvent::PAGE_VIEW, 'label' => 'Kunjungan', 'count' => $total],
            ['key' => TrackingEvent::PRODUCT_VIEW, 'label' => 'Lihat produk', 'count' => $reached[TrackingEvent::PRODUCT_VIEW]],
            ['key' => TrackingEvent::CHECKOUT_CLICK, 'label' => 'Menuju checkout', 'count' => $reached[TrackingEvent::CHECKOUT_CLICK]],
            ['key' => TrackingEvent::ORDER_CREATED, 'label' => 'Pesanan masuk', 'count' => $ordersWithSession],
            ['key' => TrackingEvent::ORDER_PAID, 'label' => 'Dibayar', 'count' => $paidWithSession],
        ];

        $previous = null;
        foreach ($rows as $i => $row) {
            $rows[$i]['of_total'] = $total > 0 ? round($row['count'] / $total * 100, 1) : 0.0;
            $rows[$i]['of_previous'] = $previous > 0 ? round($row['count'] / $previous * 100, 1) : null;
            $rows[$i]['drop'] = $previous !== null ? max(0, $previous - $row['count']) : null;
            $previous = $row['count'];
        }

        return $rows;
    }

    /**
     * Ringkasan angka utama.
     */
    public function summary(): array
    {
        $sessions = $this->sessionsInRange();
        $orders = $this->ordersInRange();
        $paid = (clone $orders)->whereIn('state', ['completed', 'processing']);

        $sessionCount = $sessions->count();
        $paidCount = (clone $paid)->count();

        return [
            'sessions' => $sessionCount,
            'page_views' => TrackingEvent::where('name', TrackingEvent::PAGE_VIEW)
                ->whereBetween('occurred_at', [$this->since, $this->until])->count(),
            'product_views' => TrackingEvent::where('name', TrackingEvent::PRODUCT_VIEW)
                ->whereBetween('occurred_at', [$this->since, $this->until])->count(),
            'checkout_clicks' => TrackingEvent::where('name', TrackingEvent::CHECKOUT_CLICK)
                ->whereBetween('occurred_at', [$this->since, $this->until])->count(),
            'orders' => (clone $orders)->count(),
            'orders_paid' => $paidCount,
            'revenue' => (int) (clone $paid)->sum('amount_raw'),
            'commission' => (int) (clone $paid)->sum('fee'),
            'conversion' => $sessionCount > 0 ? round($paidCount / $sessionCount * 100, 2) : 0.0,
            'attributed' => (clone $orders)->whereNotNull('session_id')->count(),
        ];
    }

    /**
     * Sumber trafik, diurutkan dari yang paling banyak menghasilkan pesanan.
     */
    public function sources(int $limit = 12): array
    {
        $sessions = VisitorSession::whereBetween('started_at', [$this->since, $this->until])
            ->get(['id', 'utm_source', 'utm_medium', 'referrer_host']);

        $grouped = [];
        foreach ($sessions as $session) {
            $key = $session->source_label;
            $grouped[$key] ??= ['label' => $key, 'sessions' => 0, 'orders' => 0, 'revenue' => 0];
            $grouped[$key]['sessions']++;
        }

        $orders = $this->ordersInRange()->whereNotNull('session_id')
            ->with('session:id,utm_source,utm_medium,referrer_host')
            ->get(['id', 'session_id', 'state', 'amount_raw']);

        foreach ($orders as $order) {
            $key = $order->session?->source_label ?? 'tidak terlacak';
            $grouped[$key] ??= ['label' => $key, 'sessions' => 0, 'orders' => 0, 'revenue' => 0];
            $grouped[$key]['orders']++;

            if (in_array($order->state, ['completed', 'processing'], true)) {
                $grouped[$key]['revenue'] += (int) $order->amount_raw;
            }
        }

        foreach ($grouped as $key => $row) {
            $grouped[$key]['conversion'] = $row['sessions'] > 0
                ? round($row['orders'] / $row['sessions'] * 100, 2)
                : 0.0;
        }

        usort($grouped, fn ($a, $b) => [$b['orders'], $b['sessions']] <=> [$a['orders'], $a['sessions']]);

        return array_slice($grouped, 0, $limit);
    }

    /**
     * Produk yang paling sering dilihat dan diklik menuju checkout.
     */
    public function products(int $limit = 12): array
    {
        $views = TrackingEvent::where('name', TrackingEvent::PRODUCT_VIEW)
            ->whereBetween('occurred_at', [$this->since, $this->until])
            ->whereNotNull('item_slug')
            ->selectRaw('item_slug, COUNT(*) as total')
            ->groupBy('item_slug')->pluck('total', 'item_slug');

        $clicks = TrackingEvent::where('name', TrackingEvent::CHECKOUT_CLICK)
            ->whereBetween('occurred_at', [$this->since, $this->until])
            ->whereNotNull('item_slug')
            ->selectRaw('item_slug, COUNT(*) as total')
            ->groupBy('item_slug')->pluck('total', 'item_slug');

        $rows = [];
        foreach ($views->keys()->merge($clicks->keys())->unique() as $slug) {
            $v = (int) ($views[$slug] ?? 0);
            $c = (int) ($clicks[$slug] ?? 0);

            $rows[] = [
                'slug' => $slug,
                'views' => $v,
                'clicks' => $c,
                'rate' => $v > 0 ? round($c / $v * 100, 1) : 0.0,
            ];
        }

        usort($rows, fn ($a, $b) => [$b['clicks'], $b['views']] <=> [$a['clicks'], $a['views']]);

        return array_slice($rows, 0, $limit);
    }

    /**
     * Grafik harian: kunjungan dan pesanan.
     */
    public function daily(): array
    {
        $days = [];
        $cursor = $this->since->copy()->startOfDay();

        while ($cursor <= $this->until) {
            $days[$cursor->toDateString()] = ['date' => $cursor->copy(), 'sessions' => 0, 'orders' => 0];
            $cursor->addDay();
        }

        foreach (VisitorSession::whereBetween('started_at', [$this->since, $this->until])->get(['started_at']) as $s) {
            $key = $s->started_at->toDateString();
            if (isset($days[$key])) {
                $days[$key]['sessions']++;
            }
        }

        foreach ($this->ordersInRange()->get(['ordered_at', 'created_at']) as $o) {
            $key = ($o->ordered_at ?: $o->created_at)->toDateString();
            if (isset($days[$key])) {
                $days[$key]['orders']++;
            }
        }

        return array_values($days);
    }

    protected function sessionsInRange()
    {
        return VisitorSession::whereBetween('started_at', [$this->since, $this->until])
            ->select('id')->pluck('id');
    }

    protected function ordersInRange()
    {
        return Order::whereBetween('created_at', [$this->since, $this->until]);
    }
}

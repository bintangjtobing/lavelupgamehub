<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\TrackingEvent;
use App\Services\Analytics\VisitorTracker;
use App\Services\Mlbb\HeroStatsClient;
use App\Services\Saweria\ProductDetailMapper;
use App\Services\Saweria\SaweriaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    public function show(
        string $slug,
        SaweriaClient $client,
        ProductDetailMapper $mapper,
        Request $request,
        VisitorTracker $tracker
    ) {
        $item = CatalogItem::active()->where('slug', $slug)->firstOrFail();

        try {
            $tracker->record($request, TrackingEvent::PRODUCT_VIEW, [
                'item_slug' => $item->slug,
                'meta' => ['name' => $item->name, 'category' => $item->category],
            ]);
        } catch (Throwable $e) {
            // Statistik tidak boleh menjatuhkan halaman produk.
            Log::warning('Pencatatan lihat produk gagal.', ['reason' => $e->getMessage()]);
        }

        try {
            // This endpoint is intentionally fetched on every request. Prices are
            // never read from the locally synced catalog or a stale cache.
            $group = $client->group($item->slug);

            $data = $group === null
                ? $mapper->unavailable($item)
                : $mapper->map($item, $group);
        } catch (Throwable $exception) {
            Log::warning('Saweria product detail is unavailable.', [
                'catalog_item_id' => $item->getKey(),
                'exception' => get_class($exception),
            ]);

            $data = $mapper->unavailable($item);
        }

        return view('pages.product', $data + $this->heroStats($item, $request));
    }

    /**
     * Peringkat hero, khusus untuk produk Mobile Legends.
     *
     * Statistik ini tambahan, bukan inti halaman. Bila Moonton sedang tidak
     * bisa dihubungi, nilainya null dan bagian tersebut tidak dirender --
     * halaman produk beserta daftar harganya tetap tampil utuh.
     */
    protected function heroStats(CatalogItem $item, Request $request): array
    {
        $none = ['heroStats' => null, 'heroRank' => null, 'heroDays' => null, 'heroMetric' => null];

        if ($item->slug !== config('mlbb.slug')) {
            return $none;
        }

        $rank = (int) $request->query('rank', config('mlbb.default_rank'));
        $days = (int) $request->query('hari', config('mlbb.default_range'));
        $metric = (string) $request->query('urut', 'win_rate');

        // Nilai dari alamat halaman tidak dipercaya begitu saja
        if (! array_key_exists($rank, config('mlbb.ranks', []))) {
            $rank = (int) config('mlbb.default_rank');
        }

        if (! array_key_exists($days, config('mlbb.ranges', []))) {
            $days = (int) config('mlbb.default_range');
        }

        if (! in_array($metric, ['win_rate', 'pick_rate', 'ban_rate'], true)) {
            $metric = 'win_rate';
        }

        $stats = app(HeroStatsClient::class)->heroes($rank, $days);

        // Moonton selalu mengurutkan menurut win rate, jadi urutan untuk
        // metrik lain disusun ulang di sini.
        if ($stats && $metric !== 'win_rate') {
            usort($stats['heroes'], fn ($a, $b) => $b[$metric] <=> $a[$metric]);
        }

        return [
            'heroStats' => $stats,
            'heroRank' => $rank,
            'heroDays' => $days,
            'heroMetric' => $metric,
        ];
    }
}

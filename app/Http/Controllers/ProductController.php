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

        return view('pages.product', $data + $this->heroStats($item));
    }

    /**
     * Peringkat hero, khusus untuk produk Mobile Legends.
     *
     * Statistik ini tambahan, bukan inti halaman. Bila Moonton sedang tidak
     * bisa dihubungi, nilainya null dan bagian tersebut tidak dirender --
     * halaman produk beserta daftar harganya tetap tampil utuh.
     */
    protected function heroStats(CatalogItem $item): array
    {
        if ($item->slug !== config('mlbb.slug')) {
            return ['heroStats' => null, 'heroDays' => null];
        }

        $days = (int) config('mlbb.default_range');

        return [
            // Moonton sudah mengurutkan menurut win rate, jadi tidak ada
            // pengurutan ulang di sisi kita.
            'heroStats' => app(HeroStatsClient::class)->heroes((int) config('mlbb.default_rank'), $days),
            'heroDays' => $days,
        ];
    }
}

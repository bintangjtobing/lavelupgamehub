<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Review;
use App\Services\GameStats\GameStatsManager;

class CatalogController extends Controller
{
    public function home()
    {
        return view('pages.index', $this->catalogData() + [
            'reviews' => $this->reviews(),
            // Game yang sumbernya sedang gagal dibaca tidak ikut, sehingga
            // section menyesuaikan diri tanpa menampilkan tabel kosong.
            'gameStats' => app(GameStatsManager::class)->all(),
        ]);
    }

    public function topup()
    {
        return view('pages.topup', $this->catalogData());
    }

    /**
     * Data bersama untuk section "Paling Laris" dan penjelajah katalog.
     */
    protected function catalogData(): array
    {
        $catalog = CatalogItem::active()->orderBy('name')->get();

        // Saweria tidak mengirim tanggal rilis produk, jadi "terbaru" memakai id grup
        // sebagai perkiraan: makin besar id, makin belakangan produk itu ditambahkan.
        $newestCodes = $catalog->sortByDesc('external_id')->take(18)->pluck('code')->flip();
        $catalog->each(function ($item) use ($newestCodes) {
            $item->is_new = $newestCodes->has($item->code) ?: null;
        });

        return [
            'catalog' => $catalog,
            'bestSellers' => CatalogItem::active()->bestSellers()->get(),
            'counts' => $catalog->groupBy('category')->map->count(),
            'catalogTotal' => $catalog->count(),
        ];
    }

    protected function reviews()
    {
        return Review::published()->get();
    }
}

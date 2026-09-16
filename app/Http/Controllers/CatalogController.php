<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Review;

class CatalogController extends Controller
{
    public function home()
    {
        return view('pages.index', $this->catalogData() + [
            'reviews' => $this->reviews(),
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
        return Review::where('agree_terms', true)->orderBy('created_at', 'DESC')->get();
    }
}

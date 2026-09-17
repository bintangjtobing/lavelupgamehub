<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\TrackingEvent;
use App\Services\Analytics\VisitorTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Perantara sebelum pengunjung berpindah ke halaman pembayaran Saweria.
 *
 * Tautan langsung ke Saweria tidak bisa dihitung dari sisi kita, padahal
 * langkah inilah satu-satunya yang menghubungkan kunjungan dengan pesanan
 * yang nanti masuk lewat webhook. Karena itu klik dialihkan sebentar ke sini,
 * dicatat, lalu diteruskan.
 */
class CheckoutRedirectController extends Controller
{
    public function __invoke(Request $request, string $slug, VisitorTracker $tracker)
    {
        $item = CatalogItem::active()->where('slug', $slug)->firstOrFail();

        $product = $request->query('item');
        $product = is_string($product) && preg_match('/^[a-z0-9\-]{1,120}$/i', $product)
            ? $product
            : null;

        $target = $product
            ? $item->topup_url.'?'.http_build_query(['item' => $product], '', '&', PHP_QUERY_RFC3986)
            : $item->topup_url;

        try {
            $tracker->record($request, TrackingEvent::CHECKOUT_CLICK, [
                'item_slug' => $item->slug,
                'product_slug' => $product,
                'value' => $this->price($request),
                'meta' => ['product_name' => $this->productName($request)],
            ]);
        } catch (Throwable $e) {
            // Pencatatan gagal tidak boleh menghalangi orang membeli.
            Log::warning('Pencatatan klik checkout gagal.', ['reason' => $e->getMessage()]);
        }

        return redirect()->away($target, 302);
    }

    protected function price(Request $request): ?int
    {
        $price = $request->query('price');

        return is_numeric($price) && $price >= 0 && $price < 1_000_000_000
            ? (int) $price
            : null;
    }

    protected function productName(Request $request): ?string
    {
        $name = $request->query('name');

        return is_string($name) && $name !== ''
            ? mb_substr(strip_tags($name), 0, 120)
            : null;
    }
}

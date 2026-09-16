<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Services\Saweria\ProductDetailMapper;
use App\Services\Saweria\SaweriaClient;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    public function show(string $slug, SaweriaClient $client, ProductDetailMapper $mapper)
    {
        $item = CatalogItem::active()->where('slug', $slug)->firstOrFail();

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

        return view('pages.product', $data);
    }
}

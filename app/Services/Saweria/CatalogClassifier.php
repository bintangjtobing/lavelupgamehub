<?php

namespace App\Services\Saweria;

use App\Models\CatalogItem;

/*
 * Menentukan sebuah item katalog itu game atau produk.
 *
 * Saweria tidak mengirim kategori semacam ini. Yang ada hanya "variant", dan itu
 * soal cara pemenuhan (top up langsung vs kode voucher), bukan jenis barangnya.
 * Karena itu jenis diturunkan di sini, dengan daftar pengecualian di config/saweria.php
 * supaya keputusannya terlihat dan gampang dikoreksi.
 */
class CatalogClassifier
{
    public function classify(array $group): array
    {
        $code = $group['code'] ?? '';
        $variant = $group['variant'] ?? 'DIGITAL';

        $forceProduct = config('saweria.classification.force_product', []);
        $forceGame = config('saweria.classification.force_game', []);

        if (in_array($code, $forceProduct, true)) {
            return [CatalogItem::TYPE_PRODUCT, true];
        }

        if (in_array($code, $forceGame, true)) {
            return [CatalogItem::TYPE_GAME, true];
        }

        // Aturan dasar: top up langsung itu game, voucher itu produk.
        $type = $variant === 'DIGITAL'
            ? CatalogItem::TYPE_GAME
            : CatalogItem::TYPE_PRODUCT;

        return [$type, false];
    }
}

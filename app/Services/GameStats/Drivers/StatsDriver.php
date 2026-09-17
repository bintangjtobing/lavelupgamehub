<?php

namespace App\Services\GameStats\Drivers;

/*
 * Kontrak sumber statistik hero.
 *
 * Tiap driver mengembalikan baris dengan bentuk yang sama, sehingga tampilan
 * tidak perlu tahu game mana yang sedang dibaca:
 *
 *   name  - nama hero atau champion
 *   image - alamat gambar, atau null
 *   win   - persen, atau null bila sumbernya tidak menyediakan
 *   pick  - persen, atau null
 *   ban   - persen, atau null
 *   role  - peran hero; dipakai game yang tidak punya angka rate
 *
 * Nilai null berarti "tidak tersedia", bukan nol. Tampilan wajib
 * membedakan keduanya supaya tidak ada angka yang dikarang.
 */
interface StatsDriver
{
    /**
     * @return array<int, array{name: string, image: ?string, win: ?float, pick: ?float, ban: ?float, role: ?string}>
     */
    public function rows(int $limit): array;
}

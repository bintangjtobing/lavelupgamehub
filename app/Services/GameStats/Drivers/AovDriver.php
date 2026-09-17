<?php

namespace App\Services\GameStats\Drivers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Arena of Valor lewat rovmeta.com.
 *
 * Ini SATU-SATUNYA driver yang tidak membaca API. rovmeta tidak menyediakan
 * endpoint data; angkanya tertanam di dalam muatan React Server Component
 * milik halamannya, dan harus dikupas dari teks mentah.
 *
 * Konsekuensinya jujur saja: begitu rovmeta mengubah struktur halamannya,
 * pembacaan ini berhenti bekerja. Karena itu setiap kegagalan dibiarkan
 * melempar, dan pemanggil di atasnya memperlakukan AOV sebagai tidak
 * tersedia -- bukan menampilkan tabel kosong yang membingungkan.
 *
 * Perlu diingat pula bahwa angkanya berasal dari pertandingan pro server
 * Thailand, jadi tidak mewakili pemain umum Indonesia.
 */
class AovDriver implements StatsDriver
{
    protected const PAGE = 'https://rovmeta.com/th?_rsc=1r34m';

    /**
     * Hero yang nyaris tidak pernah dipilih dibuang.
     *
     * Tanpa ambang ini, hero dengan pick rate 0,2 persen muncul di puncak
     * dengan win rate 100 persen -- angka yang benar secara hitungan tetapi
     * tidak berarti apa-apa, dan justru menyesatkan pembaca.
     */
    protected const MIN_PICK_RATE = 2.0;

    public function rows(int $limit): array
    {
        $response = Http::timeout((int) config('gamestats.timeout', 10))
            ->connectTimeout(3)
            ->retry(1, 0, null, false)
            ->withHeaders(['RSC' => '1', 'Accept' => 'text/x-component, */*'])
            ->get(self::PAGE);

        if ($response->failed()) {
            throw new RuntimeException('rovmeta tidak bisa dihubungi (HTTP '.$response->status().').');
        }

        return $this->parse($response->body(), $limit);
    }

    /**
     * Nama hero dan blok angkanya berada di tempat terpisah dalam muatan,
     * sehingga tiap blok statistik dipasangkan dengan nama terdekat yang
     * mendahuluinya.
     */
    protected function parse(string $payload, int $limit): array
    {
        /*
         * Muatan yang sama bisa datang dalam dua bentuk: kutipnya polos, atau
         * ter-escape karena terbungkus sebagai teks JSON. Bentuknya bergantung
         * pada header permintaan, jadi escape-nya dinormalkan lebih dulu agar
         * pola di bawah cukup ditulis satu kali.
         */
        $payload = str_replace('\\"', '"', $payload);

        $names = [];
        preg_match_all('/"name_en":"([^"]+)"/', $payload, $m, PREG_OFFSET_CAPTURE);
        foreach ($m[1] as $hit) {
            $names[] = [$hit[0], $hit[1]];
        }

        $stats = [];
        preg_match_all(
            '/"stats":\{"win_rate":([0-9.]+),"pick_rate":([0-9.]+),"ban_rate":([0-9.]+)/',
            $payload, $s, PREG_OFFSET_CAPTURE
        );

        foreach ($s[0] as $i => $hit) {
            $stats[] = [
                'pos' => $hit[1],
                'win' => (float) $s[1][$i][0],
                'pick' => (float) $s[2][$i][0],
                'ban' => (float) $s[3][$i][0],
            ];
        }

        if ($names === [] || $stats === []) {
            throw new RuntimeException('Muatan rovmeta tidak lagi berisi data yang dikenali.');
        }

        $icons = $this->icons($payload);
        $rows = [];
        $seen = [];

        foreach ($stats as $stat) {
            $name = null;

            foreach ($names as [$candidate, $pos]) {
                if ($pos < $stat['pos']) {
                    $name = $candidate;
                } else {
                    break;
                }
            }

            if ($name === null || isset($seen[$name]) || $stat['pick'] < self::MIN_PICK_RATE) {
                continue;
            }

            $seen[$name] = true;

            $rows[] = [
                'name' => $name,
                'image' => $icons[$name] ?? null,
                'win' => $stat['win'],
                'pick' => $stat['pick'],
                'ban' => $stat['ban'],
                'role' => null,
            ];
        }

        usort($rows, fn ($a, $b) => $b['win'] <=> $a['win']);

        return array_slice($rows, 0, $limit);
    }

    /**
     * Gambar hero dipasangkan dengan namanya bila keduanya berdekatan.
     * Alamat hanya diterima dari CDN Garena.
     */
    protected function icons(string $payload): array
    {
        $icons = [];

        preg_match_all(
            '/"name_en":"([^"]+)".{0,400}?"icon_url":"(https:[^"]+)"/s',
            $payload, $m
        );

        foreach ($m[1] as $i => $name) {
            // Muatan Next.js menuliskan garis miring sebagai \/
            $url = str_replace('\/', '/', $m[2][$i]);
            $host = parse_url($url, PHP_URL_HOST);

            if (is_string($host) && str_ends_with($host, 'garenanow.com') && ! isset($icons[$name])) {
                $icons[$name] = $url;
            }
        }

        return $icons;
    }
}

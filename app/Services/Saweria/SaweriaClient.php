<?php

namespace App\Services\Saweria;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Pembungkus API toko top up Saweria.
 *
 * Catatan penting: endpoint ini internal, tidak ada dokumentasi publiknya, dan
 * Saweria bisa mengubahnya sewaktu-waktu tanpa pemberitahuan. Aman dipakai untuk
 * menarik katalog dan mencatat pesanan; jangan dijadikan tumpuan transaksi.
 */
class SaweriaClient
{
    protected const MAX_CATALOG_PAGES = 100;

    protected string $baseUrl;

    protected ?string $username;

    protected ?string $streamerId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('saweria.base_url'), '/');
        $this->username = config('saweria.username');
        $this->streamerId = config('saweria.streamer_id');
    }

    protected function request(?int $timeout = null)
    {
        return Http::connectTimeout(max(1, (int) config('saweria.connect_timeout', 3)))
            ->timeout($timeout ?? max(1, (int) config('saweria.request_timeout', 15)))
            ->retry(
                max(1, (int) config('saweria.request_attempts', 2)),
                max(0, (int) config('saweria.retry_delay_ms', 250)),
                null,
                false
            )
            ->withHeaders([
                'Accept' => 'application/json',
                'Origin' => 'https://saweria.co',
                'Referer' => 'https://saweria.co/',
                'User-Agent' => 'levelupgamehub-sync/1.0',
            ]);
    }

    /**
     * Seluruh grup produk di toko, ditarik halaman demi halaman.
     */
    public function productGroups(int $perPage = 100): array
    {
        if ($perPage < 1 || $perPage > 100) {
            throw new RuntimeException('Ukuran halaman katalog Saweria tidak valid.');
        }

        $groups = [];
        $seenCodes = [];
        $page = 1;
        $expectedTotalPages = null;
        $expectedTotalData = null;

        do {
            $response = $this->request()->get("{$this->baseUrl}/game-vouchers/groups", array_filter([
                'page' => $page,
                'limit' => $perPage,
                'username' => $this->username,
                'streamer_id' => $this->streamerId,
            ]));

            if ($response->failed()) {
                throw new RuntimeException("Gagal mengambil katalog Saweria (HTTP {$response->status()})");
            }

            $data = $response->json('data');

            if (! is_array($data)
                || ! isset($data['product_groups'])
                || ! is_array($data['product_groups'])
                || ! array_is_list($data['product_groups'])
                || ! isset($data['page'])
                || ! is_array($data['page'])) {
                throw new RuntimeException('Respons katalog Saweria tidak valid.');
            }

            $pageData = $data['page'];
            $totalPages = $pageData['total'] ?? null;
            $currentPage = $pageData['current'] ?? null;
            $totalData = $pageData['total_data'] ?? null;

            if (! is_int($totalPages) || $totalPages < 1 || $totalPages > self::MAX_CATALOG_PAGES
                || ! is_int($currentPage) || $currentPage !== $page
                || ! is_int($totalData) || $totalData < 0) {
                throw new RuntimeException('Metadata halaman katalog Saweria tidak valid.');
            }

            if ($expectedTotalPages === null) {
                $expectedTotalPages = $totalPages;
                $expectedTotalData = $totalData;
            } elseif ($totalPages !== $expectedTotalPages || $totalData !== $expectedTotalData) {
                throw new RuntimeException('Metadata halaman katalog Saweria berubah saat sinkronisasi.');
            }

            $pageGroups = $data['product_groups'];

            if ($page < $totalPages && $pageGroups === []) {
                throw new RuntimeException('Halaman antara katalog Saweria kosong.');
            }

            foreach ($pageGroups as $group) {
                $code = is_array($group) && is_string($group['code'] ?? null)
                    ? trim($group['code'])
                    : '';
                $slug = is_array($group) && is_string($group['slug'] ?? null)
                    ? trim($group['slug'])
                    : '';

                if ($code === '' || $slug === '') {
                    throw new RuntimeException('Item katalog Saweria tidak memiliki kode atau slug yang valid.');
                }

                $codeKey = strtolower($code);

                if (isset($seenCodes[$codeKey])) {
                    throw new RuntimeException('Respons katalog Saweria memuat kode item duplikat.');
                }

                $seenCodes[$codeKey] = true;
                $groups[] = $group;
            }

            $page++;
        } while ($page <= $expectedTotalPages);

        if (count($groups) !== $expectedTotalData) {
            throw new RuntimeException('Jumlah item katalog Saweria tidak sesuai metadata halaman.');
        }

        return $groups;
    }

    /**
     * Detail satu grup: daftar produk, harga, dan form input (User ID / Zone ID).
     */
    public function group(string $slug): ?array
    {
        $response = $this->request(max(1, (int) config('saweria.detail_timeout', 8)))
            // A public page must not hold a PHP worker through repeated outages.
            ->retry(1, 0, null, false)
            ->get("{$this->baseUrl}/game-vouchers/".rawurlencode($slug), array_filter([
                'username' => $this->username,
                'streamer_id' => $this->streamerId,
            ]));

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException("Gagal mengambil detail katalog Saweria (HTTP {$response->status()})");
        }

        $group = $response->json('data.game_vouchers');

        if (! is_array($group)) {
            throw new RuntimeException('Respons detail katalog Saweria tidak valid.');
        }

        return $group;
    }

    /**
     * Detail satu pesanan. Inilah yang membuat webhook berguna: callback Saweria
     * hanya membawa id, dan endpoint ini yang mengubah id itu jadi detail produk.
     */
    public function trackingOrder(string $id): ?array
    {
        $response = $this->request()->get("{$this->baseUrl}/game-vouchers/tracking-order/{$id}");

        if ($response->failed()) {
            return null;
        }

        return $response->json('data');
    }
}

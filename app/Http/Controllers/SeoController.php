<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Support\Seo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(Seo $seo): Response
    {
        $urls = collect(array_keys(config('seo.pages', [])))
            ->map(fn (string $path): string => $seo->url($path))
            ->merge(
                CatalogItem::active()
                    ->orderBy('slug')
                    ->pluck('slug')
                    ->map(fn (string $slug): string => $seo->url('/topup/'.rawurlencode($slug)))
            );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc></url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(Seo $seo): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /games/search',
            'Disallow: /api/',
            // Panel pengelola tidak untuk publik, dan /ke-checkout hanya
            // pengalih menuju Saweria sehingga tidak perlu diindeks.
            'Disallow: /admin',
            'Disallow: /ke-checkout/',
            '',
            'Sitemap: '.$seo->url('/sitemap.xml'),
            '',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function llms(Seo $seo): Response
    {
        $lines = [
            '# LevelUp Market',
            '',
            '> Katalog top up game, voucher, dan produk digital. Pembayaran produk dilakukan melalui Saweria.',
            '',
            '## Halaman utama',
            '',
        ];

        foreach (config('seo.pages', []) as $path => $page) {
            $lines[] = '- ['.$this->markdownText((string) $page['label']).']('.$seo->url((string) $path).'): '
                .$this->markdownText((string) $page['description']);
        }

        $lines[] = '';
        $lines[] = '## Katalog aktif';
        $lines[] = '';

        CatalogItem::active()->orderBy('name')->each(function (CatalogItem $item) use (&$lines, $seo): void {
            $lines[] = '- ['.$this->markdownText($item->name).']('
                .$seo->url('/topup/'.rawurlencode($item->slug)).')';
        });

        $lines[] = '';
        $lines[] = '## Informasi';
        $lines[] = '';
        $lines[] = '- Ketersediaan katalog dapat berubah.';
        $lines[] = '- Publisher situs: LevelUp Market. Nama penerbit game pada katalog berbeda dari publisher situs.';
        $lines[] = '- Harga paket diambil dari Saweria saat halaman detail dibuka; total pembayaran dan biaya mengikuti checkout Saweria.';
        $lines[] = '- Pilihan Populer adalah kurasi editorial, bukan peringkat transaksi.';
        $lines[] = '- Situs ini tidak menyatakan afiliasi resmi dengan penerbit game yang produknya tercantum.';
        $lines[] = '';
        $lines[] = '## Akun sosial LevelUp Market';
        $lines[] = '';
        foreach (config('seo.publisher.same_as', []) as $socialUrl) {
            $lines[] = '- <'.$socialUrl.'>';
        }
        $lines[] = '';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function markdownText(string $value): string
    {
        $value = preg_replace('/[\r\n]+/', ' ', trim($value)) ?? '';

        return str_replace(['\\', '[', ']', '<', '>'], ['\\\\', '\\[', '\\]', '\\<', '\\>'], $value);
    }
}

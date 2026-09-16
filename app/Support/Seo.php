<?php

namespace App\Support;

use App\Models\CatalogItem;
use Illuminate\Http\Request;

class Seo
{
    public function forRequest(Request $request, ?CatalogItem $item = null): array
    {
        return $item === null
            ? $this->forPath('/'.ltrim($request->path(), '/'))
            : $this->forProduct($item);
    }

    public function forPath(string $path): array
    {
        $path = $this->normalizePath($path);
        $page = config("seo.pages.{$path}");

        if (! is_array($page)) {
            $page = [
                'title' => config('seo.site_name'),
                'description' => config('seo.description'),
                'label' => config('seo.site_name'),
            ];
        }

        $metadata = $this->metadata(
            (string) $page['title'],
            (string) $page['description'],
            $path,
            (string) $page['label']
        );
        $terms = config('seo_keywords.static', [])[$path] ?? [];
        $metadata['keywords'] = $this->keywords($terms);

        return $metadata;
    }

    public function forProduct(CatalogItem $item): array
    {
        $name = trim((string) $item->name);
        $copy = $this->productCopy($item);
        $description = $copy['description'];
        $path = '/topup/'.rawurlencode((string) $item->slug);

        $metadata = $this->metadata(
            $copy['title'],
            $description,
            $path,
            $name,
            [
                ['name' => 'Beranda', 'path' => '/'],
                ['name' => 'Katalog Top Up', 'path' => '/topup'],
                ['name' => $name, 'path' => $path],
            ]
        );

        $metadata['keywords'] = $this->keywords($copy);
        $metadata['intro'] = $copy['intro'];

        $catalogGroup = array_filter([
            '@type' => 'Thing',
            '@id' => $metadata['canonical'].'#catalog-group',
            'name' => $name,
            'description' => $description,
            'image' => config('catalog_images.'.$item->slug)
                ? $this->url(config('catalog_images.'.$item->slug)) : ($item->cover ?: null),
            'url' => $metadata['canonical'],
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $metadata['structured_data'][0]['@graph'][2]['@type'] = 'CollectionPage';
        $metadata['structured_data'][0]['@graph'][2]['about'] = ['@id' => $catalogGroup['@id']];
        $metadata['structured_data'][0]['@graph'][] = $catalogGroup;

        return $metadata;
    }

    public function productCopy(CatalogItem $item): array
    {
        $fallbacks = config('seo_keywords.product_fallback');
        $copy = array_replace(
            $fallbacks[$item->category] ?? $fallbacks['default'],
            config('seo_keywords.products', [])[$item->slug] ?? []
        );
        array_walk_recursive($copy, static function (&$value) use ($item): void {
            $value = str_replace('{name}', trim((string) $item->name), $value);
        });

        return $copy;
    }

    protected function keywords(array $copy): string
    {
        return implode(', ', array_unique(array_filter([
            $copy['primary'] ?? '', ...($copy['secondary'] ?? []),
        ])));
    }

    public function baseUrl(): string
    {
        return rtrim((string) config('seo.url', 'https://levelupgamehub.com'), '/');
    }

    public function url(string $path = '/'): string
    {
        $path = $this->normalizePath($path);

        return $path === '/' ? $this->baseUrl().'/' : $this->baseUrl().$path;
    }

    protected function metadata(
        string $title,
        string $description,
        string $path,
        string $label,
        ?array $breadcrumbs = null
    ): array {
        $canonical = $this->url($path);
        $publisherName = (string) config('seo.publisher.name', config('seo.site_name'));
        $organizationId = $this->baseUrl().'/#organization';
        $websiteId = $this->baseUrl().'/#website';
        $webpageId = $canonical.'#webpage';
        $socialImage = (string) config('seo.social_image');
        if (! str_starts_with($socialImage, 'https://')) {
            $socialImage = $this->url($socialImage);
        }
        $breadcrumbs ??= $path === '/'
            ? [['name' => 'Beranda', 'path' => '/']]
            : [
                ['name' => 'Beranda', 'path' => '/'],
                ['name' => $label, 'path' => $path],
            ];

        $breadcrumbItems = [];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string) $breadcrumb['name'],
                'item' => $this->url((string) $breadcrumb['path']),
            ];
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'image' => $socialImage,
            'image_alt' => 'LevelUp Market — top up game dan voucher digital',
            'locale' => (string) config('seo.locale', 'id_ID'),
            'site_name' => (string) config('seo.site_name', 'LevelUp Market'),
            'structured_data' => [[
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        '@id' => $organizationId,
                        'name' => $publisherName,
                        'url' => $this->url('/'),
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $this->url((string) config('seo.logo')),
                        ],
                        'sameAs' => array_values(config('seo.publisher.same_as', [])),
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => $websiteId,
                        'url' => $this->url('/'),
                        'name' => (string) config('seo.site_name', 'LevelUp Market'),
                        'publisher' => ['@id' => $organizationId],
                        'inLanguage' => 'id-ID',
                    ],
                    [
                        '@type' => 'WebPage',
                        '@id' => $webpageId,
                        'url' => $canonical,
                        'name' => $title,
                        'description' => $description,
                        'isPartOf' => ['@id' => $websiteId],
                        'publisher' => ['@id' => $organizationId],
                        'breadcrumb' => ['@id' => $canonical.'#breadcrumb'],
                        'inLanguage' => 'id-ID',
                    ],
                    [
                        '@type' => 'BreadcrumbList',
                        '@id' => $canonical.'#breadcrumb',
                        'itemListElement' => $breadcrumbItems,
                    ],
                ],
            ]],
        ];
    }

    protected function normalizePath(string $path): string
    {
        $path = '/'.trim(parse_url($path, PHP_URL_PATH) ?: '/', '/');

        return $path === '//' ? '/' : $path;
    }
}

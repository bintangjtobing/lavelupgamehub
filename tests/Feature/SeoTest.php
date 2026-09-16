<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Support\Seo;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['seo.url' => 'https://levelupgamehub.com']);
    }

    public function test_static_pages_have_unique_metadata_and_canonical_urls(): void
    {
        $seo = app(Seo::class);
        $pages = config('seo.pages');
        $metadata = collect(array_keys($pages))->map(fn (string $path): array => $seo->forPath($path));

        $this->assertCount(8, $metadata);
        $this->assertCount(8, $metadata->pluck('title')->unique());
        $this->assertCount(8, $metadata->pluck('description')->unique());
        $this->assertSame('https://levelupgamehub.com/', $metadata->first()['canonical']);
        $this->assertSame('https://levelupgamehub.com/topup', $metadata[1]['canonical']);
    }

    public function test_keyword_copy_is_specific_to_product_and_category(): void
    {
        $seo = app(Seo::class);
        $ml = $this->catalogItem(['slug' => 'mobile-legends-bang-bang', 'name' => 'Mobile Legends']);
        $this->assertStringContainsString('diamond MLBB', $seo->forProduct($ml)['keywords']);
        $this->assertStringContainsString('Zone ID', $seo->forProduct($ml)['intro']);
        $voucher = $this->catalogItem(['code' => 'VOUCHER', 'external_id' => 2, 'slug' => 'sample-voucher', 'name' => 'Sample Voucher', 'variant' => 'VOUCHER']);
        $this->assertStringStartsWith('Voucher Sample Voucher', $seo->forProduct($voucher)['title']);
        foreach (array_keys(config('seo.pages')) as $path) {
            $this->assertNotEmpty($seo->forPath($path)['keywords']);
        }
    }

    public function test_canonical_url_ignores_the_incoming_host_and_query_string(): void
    {
        $metadata = app(Seo::class)->forRequest(
            Request::create('http://preview.example.test/topup?page=4&utm_source=test')
        );

        $this->assertSame('https://levelupgamehub.com/topup', $metadata['canonical']);
    }

    public function test_all_static_pages_render_the_shared_seo_head(): void
    {
        foreach (config('seo.pages') as $path => $page) {
            $response = $this->get('http://preview.example.test'.$path.'?utm_source=test');

            $response->assertOk()
                ->assertSee('<title>'.e($page['title']).'</title>', false)
                ->assertSee('<link rel="canonical" href="'.config('seo.url').($path === '/' ? '/' : $path).'">', false)
                ->assertSee('<script type="application/ld+json">', false);

            $this->assertSame(1, substr_count($response->getContent(), '<link rel="canonical"'));
            preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $scripts);
            $this->assertNotEmpty($scripts[1]);
            foreach ($scripts[1] as $json) {
                $this->assertIsArray(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
            }
        }
    }

    public function test_product_head_is_safe_and_uses_the_configured_canonical_host(): void
    {
        $item = $this->catalogItem([
            'name' => 'Game <script>alert("x")</script>',
            'slug' => 'game-special',
            'cover' => 'https://cdn.example.test/cover.png',
        ]);

        $seo = app(Seo::class)->forProduct($item);
        $html = view('partials.seo-head', compact('seo'))->render();

        $this->assertStringContainsString('<title>Top Up Game &lt;script&gt;', $html);
        $this->assertStringContainsString('https://levelupgamehub.com/topup/game-special', $html);
        $this->assertStringContainsString('twitter:card" content="summary_large_image', $html);
        $this->assertStringContainsString(config('seo.social_image'), $html);
        $this->assertStringContainsString('\\u003Cscript\\u003E', $html);
        $this->assertStringNotContainsString('<script>alert("x")', $html);

        $graph = $seo['structured_data'][0]['@graph'];
        $this->assertSame(['Organization', 'WebSite', 'CollectionPage', 'BreadcrumbList', 'Thing'], array_column($graph, '@type'));
        $this->assertSame(['@id' => $seo['canonical'].'#catalog-group'], $graph[2]['about']);
        $this->assertSame(['@id' => 'https://levelupgamehub.com/#organization'], $graph[2]['publisher']);
        $this->assertNotContains('Product', array_column($graph, '@type'));
    }

    public function test_product_layout_auto_detects_its_catalog_item_for_metadata(): void
    {
        $item = $this->catalogItem(['name' => 'Mobile Legends', 'slug' => 'mobile-legends']);
        Http::fake(['*' => Http::response([
            'data' => ['game_vouchers' => ['products' => []]],
        ])]);

        $this->get('http://preview.example.test/topup/'.$item->slug.'?campaign=test')
            ->assertOk()
            ->assertSee('<title>Top Up Mobile Legends | LevelUp Market</title>', false)
            ->assertSee('<link rel="canonical" href="https://levelupgamehub.com/topup/mobile-legends">', false)
            ->assertSee('"@type":"CollectionPage"', false)
            ->assertDontSee('"@type":"Product"', false);
    }

    public function test_sitemap_lists_static_and_active_product_urls_without_query_pages_or_fake_dates(): void
    {
        $active = $this->catalogItem(['slug' => 'active-game']);
        $inactive = $this->catalogItem([
            'code' => 'INACTIVE',
            'external_id' => 2,
            'slug' => 'inactive-game',
            'status' => 'INACTIVE',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('https://levelupgamehub.com/topup/'.$active->slug, false)
            ->assertDontSee($inactive->slug)
            ->assertDontSee('?page=', false)
            ->assertDontSee('<lastmod>', false);

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertCount(9, $xml->url);
    }

    public function test_robots_and_llms_publish_canonical_factual_directories(): void
    {
        $item = $this->catalogItem(['name' => 'Mobile Legends', 'slug' => 'mobile-legends']);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: https://levelupgamehub.com/sitemap.xml');

        $this->get('/llms.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->assertSee('# LevelUp Market')
            ->assertSee('['.$item->name.'](https://levelupgamehub.com/topup/'.$item->slug.')', false)
            ->assertSee('tidak menyatakan afiliasi resmi')
            ->assertDontSee('guarantee', false);
    }

    protected function catalogItem(array $overrides = []): CatalogItem
    {
        return CatalogItem::create(array_merge([
            'code' => 'ACTIVE',
            'external_id' => 1,
            'name' => 'Game One',
            'slug' => 'game-one',
            'publisher' => 'Game Publisher',
            'cover' => 'https://cdn.example.test/game-one.png',
            'type' => CatalogItem::TYPE_GAME,
            'variant' => 'DIGITAL',
            'status' => 'ACTIVE',
        ], $overrides));
    }
}

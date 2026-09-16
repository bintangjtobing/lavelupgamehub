<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductDetailTest extends TestCase
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

        config([
            'saweria.base_url' => 'https://saweria.test',
            'saweria.username' => 'levelup',
            'saweria.streamer_id' => 'streamer-1',
            'saweria.store_url' => 'https://saweria.co/levelup/toko-top-up',
            'saweria.request_attempts' => 1,
            'saweria.detail_timeout' => 2,
        ]);

        Http::preventStrayRequests();
    }

    public function test_it_renders_fresh_normalized_products_and_informational_metadata(): void
    {
        $item = $this->catalogItem();

        Http::fake([
            'https://saweria.test/game-vouchers/game-one*' => Http::response([
                'data' => ['game_vouchers' => [
                    'description' => 'Dikirim langsung ke akunmu',
                    'digital_form' => [
                        'description' => 'Temukan ID di profil game.',
                        'fields' => [[
                            'key' => 'userId',
                            'label' => 'User ID',
                            'input_type' => 'number',
                            'placeholder' => 'Masukkan User ID',
                            'required' => true,
                        ]],
                    ],
                    'faq' => [
                        'how_to_order' => [
                            'question' => 'Bagaimana cara membeli?',
                            'answer' => 'Pilih nominal lalu lanjut ke Saweria.',
                        ],
                        'others' => [[
                            'question' => 'Berapa lama prosesnya?',
                            'answer' => 'Biasanya instan.',
                        ]],
                    ],
                    'products' => [[
                        'product_id' => 1218,
                        'product_name' => '5 Diamonds',
                        'slug' => '5 diamonds & bonus',
                        'status' => 'ACTIVE',
                        'category' => ['name' => 'Diamond'],
                        'pricing' => [
                            'currency' => 'IDR',
                            'initiated_price' => null,
                            'selling_price' => 1625,
                        ],
                    ]],
                ]],
            ]),
        ]);

        $response = $this->get(route('topup.show', $item->slug));

        $response->assertOk()
            ->assertViewIs('pages.product')
            ->assertViewHas('item', fn ($value) => $value->is($item))
            ->assertViewHas('description', 'Dikirim langsung ke akunmu')
            ->assertViewHas('instructions', 'Temukan ID di profil game.')
            ->assertViewHas('unavailable', false)
            ->assertViewHas('fields', fn ($fields) => $fields === [[
                'label' => 'User ID',
                'placeholder' => 'Masukkan User ID',
                'input_type' => 'number',
                'required' => true,
            ]])
            ->assertViewHas('faqs', fn ($faqs) => count($faqs) === 2)
            ->assertViewHas('products', function ($products) {
                return $products === [[
                    'id' => 1218,
                    'name' => '5 Diamonds',
                    'category' => 'Diamond',
                    'price' => 1625.0,
                    'checkout_url' => 'https://saweria.co/levelup/toko-top-up/game-one?item=5%20diamonds%20%26%20bonus',
                ]];
            });

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://saweria.test/game-vouchers/game-one?username=levelup&streamer_id=streamer-1';
        });
    }

    public function test_it_filters_disabled_non_idr_and_invalid_prices(): void
    {
        $item = $this->catalogItem();

        $products = [
            $this->remoteProduct(['status' => 'INACTIVE']),
            $this->remoteProduct(['pricing' => ['currency' => 'USD', 'selling_price' => 10]]),
            $this->remoteProduct(['pricing' => ['currency' => 'IDR', 'selling_price' => 0]]),
            $this->remoteProduct(['pricing' => ['currency' => 'IDR', 'selling_price' => 'not-a-price']]),
            $this->remoteProduct(['product_id' => null]),
            $this->remoteProduct(['slug' => '']),
        ];

        Http::fake([
            '*' => Http::response([
                'data' => ['game_vouchers' => [
                    'digital_form' => ['fields' => 'invalid'],
                    'products' => $products,
                ]],
            ]),
        ]);

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('products', [])
            ->assertViewHas('fields', [])
            ->assertViewHas('unavailable', false);
    }

    public function test_it_does_not_reuse_product_data_between_requests(): void
    {
        $item = $this->catalogItem();

        Http::fakeSequence()
            ->push(['data' => ['game_vouchers' => [
                'products' => [$this->remoteProduct(['product_name' => 'Harga pertama'])],
            ]]])
            ->push(['data' => ['game_vouchers' => [
                'products' => [$this->remoteProduct(['product_name' => 'Harga terbaru'])],
            ]]]);

        $this->get(route('topup.show', $item->slug))
            ->assertViewHas('products', fn ($products) => $products[0]['name'] === 'Harga pertama');

        $this->get(route('topup.show', $item->slug))
            ->assertViewHas('products', fn ($products) => $products[0]['name'] === 'Harga terbaru');

        Http::assertSentCount(2);
    }

    public function test_saweria_failures_are_rendered_as_temporarily_unavailable(): void
    {
        $item = $this->catalogItem();
        config(['saweria.request_attempts' => 3]);

        Http::fake(['*' => Http::response(['message' => 'upstream error'], 503)]);

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('item', fn ($value) => $value->is($item))
            ->assertViewHas('products', [])
            ->assertViewHas('unavailable', true)
            ->assertSee('Harga belum bisa dimuat');

        Http::assertSentCount(1);
    }

    public function test_connection_failures_are_rendered_as_temporarily_unavailable(): void
    {
        $item = $this->catalogItem();

        Http::fake(fn () => throw new ConnectionException('connection failed'));

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('products', [])
            ->assertViewHas('unavailable', true)
            ->assertSee('Harga belum bisa dimuat');
    }

    public function test_a_missing_remote_group_is_rendered_as_temporarily_unavailable(): void
    {
        $item = $this->catalogItem();

        Http::fake(['*' => Http::response([], 404)]);

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('products', [])
            ->assertViewHas('unavailable', true);
    }

    public function test_a_successful_empty_group_uses_the_empty_product_state(): void
    {
        $item = $this->catalogItem();

        Http::fake(['*' => Http::response([
            'data' => ['game_vouchers' => ['products' => []]],
        ])]);

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('products', [])
            ->assertViewHas('unavailable', false);
    }

    public function test_a_malformed_success_response_is_treated_as_unavailable(): void
    {
        $item = $this->catalogItem();

        Http::fake(['*' => Http::response([
            'data' => ['game_vouchers' => ['products' => 'invalid']],
        ])]);

        $this->get(route('topup.show', $item->slug))
            ->assertOk()
            ->assertViewHas('products', [])
            ->assertViewHas('unavailable', true);
    }

    public function test_unknown_and_inactive_local_items_return_404_without_calling_saweria(): void
    {
        $this->catalogItem(['slug' => 'inactive', 'status' => 'INACTIVE']);
        Http::fake();

        $this->get('/topup/not-in-the-catalog')->assertNotFound();
        $this->get('/topup/inactive')->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_remote_text_is_escaped_and_product_slug_cannot_inject_query_parameters(): void
    {
        $item = $this->catalogItem(['name' => '<script>local()</script>']);
        $product = $this->remoteProduct([
            'product_name' => '<img src=x onerror=remote()>',
            'slug' => 'safe&redirect=https://evil.test/" onclick="bad()',
        ]);

        Http::fake(['*' => Http::response([
            'data' => ['game_vouchers' => [
                'description' => '<script>description()</script>',
                'products' => [$product],
            ]],
        ])]);

        $response = $this->get(route('topup.show', $item->slug));

        $response->assertOk()
            ->assertSee('&lt;script&gt;local()&lt;/script&gt;', false)
            ->assertSee('&lt;img src=x onerror=remote()&gt;', false)
            ->assertSee('item=safe%26redirect%3Dhttps%3A%2F%2Fevil.test%2F%22%20onclick%3D%22bad%28%29', false)
            ->assertDontSee('<script>local()</script>', false)
            ->assertDontSee('<script>description()</script>', false)
            ->assertDontSee('onclick="bad()', false);
    }

    protected function catalogItem(array $attributes = []): CatalogItem
    {
        return CatalogItem::create(array_merge([
            'code' => 'DG-GAMEONE',
            'external_id' => 10,
            'name' => 'Game One',
            'slug' => 'game-one',
            'publisher' => 'Publisher',
            'cover' => 'https://cdn.test/game.jpg',
            'type' => CatalogItem::TYPE_GAME,
            'variant' => 'DIGITAL',
            'type_overridden' => false,
            'status' => 'ACTIVE',
        ], $attributes));
    }

    protected function remoteProduct(array $attributes = []): array
    {
        return array_replace_recursive([
            'product_id' => 1,
            'product_name' => '10 Diamonds',
            'slug' => '10-diamonds',
            'status' => 'ACTIVE',
            'category' => ['name' => 'Diamond'],
            'pricing' => [
                'currency' => 'IDR',
                'selling_price' => 5000,
            ],
        ], $attributes);
    }
}

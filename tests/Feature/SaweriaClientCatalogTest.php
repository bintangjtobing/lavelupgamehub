<?php

namespace Tests\Feature;

use App\Services\Saweria\SaweriaClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class SaweriaClientCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saweria.base_url' => 'https://saweria.test',
            'saweria.username' => 'store',
            'saweria.streamer_id' => 'streamer',
            'saweria.request_attempts' => 1,
        ]);

        Http::preventStrayRequests();
    }

    public function test_it_returns_a_complete_consistent_paginated_catalog(): void
    {
        Http::fakeSequence()
            ->push($this->page(1, 2, 3, [
                $this->group('GAME-A', 'game-a'),
                $this->group('GAME-B', 'game-b'),
            ]))
            ->push($this->page(2, 2, 3, [
                $this->group('GAME-C', 'game-c'),
            ]));

        $groups = app(SaweriaClient::class)->productGroups(2);

        $this->assertSame(['GAME-A', 'GAME-B', 'GAME-C'], array_column($groups, 'code'));
        Http::assertSentCount(2);
    }

    public function test_it_rejects_malformed_data_or_product_group_envelopes(): void
    {
        foreach ([
            ['data' => null],
            ['data' => ['product_groups' => 'invalid', 'page' => ['current' => 1, 'total' => 1, 'total_data' => 0]]],
            ['data' => ['product_groups' => [], 'page' => 'invalid']],
        ] as $payload) {
            $this->assertRejected([$payload]);
        }
    }

    public function test_it_rejects_invalid_or_changing_page_metadata(): void
    {
        $this->assertRejected([$this->page(1, 0, 1, [$this->group('GAME-A', 'game-a')])]);
        $this->assertRejected([$this->page(1, '1', 1, [$this->group('GAME-A', 'game-a')])]);
        $this->assertRejected([$this->page(2, 2, 1, [$this->group('GAME-A', 'game-a')])]);
        $this->assertRejected([
            $this->page(1, 2, 2, [$this->group('GAME-A', 'game-a')]),
            $this->page(2, 3, 2, [$this->group('GAME-B', 'game-b')]),
        ]);
        $this->assertRejected([
            $this->page(1, 2, 2, [$this->group('GAME-A', 'game-a')]),
            $this->page(2, 2, 3, [$this->group('GAME-B', 'game-b')]),
        ]);
    }

    public function test_it_rejects_an_empty_intermediate_page_and_incomplete_final_count(): void
    {
        $this->assertRejected([$this->page(1, 2, 1, [])]);
        $this->assertRejected([
            $this->page(1, 2, 3, [
                $this->group('GAME-A', 'game-a'),
                $this->group('GAME-B', 'game-b'),
            ]),
            $this->page(2, 2, 3, []),
        ]);
    }

    public function test_it_rejects_groups_without_valid_codes_or_slugs(): void
    {
        foreach ([
            ['slug' => 'game-a'],
            ['code' => 'GAME-A'],
            ['code' => ' ', 'slug' => 'game-a'],
            ['code' => 'GAME-A', 'slug' => ' '],
            'not-an-array',
        ] as $group) {
            $this->assertRejected([$this->page(1, 1, 1, [$group])]);
        }
    }

    public function test_it_rejects_duplicate_codes_within_or_across_pages(): void
    {
        $this->assertRejected([$this->page(1, 1, 2, [
            $this->group('GAME-A', 'game-a'),
            $this->group('GAME-A', 'game-a-copy'),
        ])]);

        $this->assertRejected([
            $this->page(1, 2, 2, [$this->group('GAME-A', 'game-a')]),
            $this->page(2, 2, 2, [$this->group('game-a', 'game-a-copy')]),
        ]);
    }

    public function test_it_rejects_unbounded_page_sizes(): void
    {
        $this->assertRejectedFor(fn () => app(SaweriaClient::class)->productGroups(0));
        $this->assertRejectedFor(fn () => app(SaweriaClient::class)->productGroups(101));
        Http::assertNothingSent();
    }

    protected function assertRejected(array $responses): void
    {
        $sequence = Http::fakeSequence();

        foreach ($responses as $response) {
            $sequence->push($response);
        }

        $this->assertRejectedFor(fn () => app(SaweriaClient::class)->productGroups());
    }

    protected function assertRejectedFor(callable $operation): void
    {
        try {
            $operation();
            $this->fail('Malformed catalog response was accepted.');
        } catch (RuntimeException) {
            $this->addToAssertionCount(1);
        }
    }

    protected function page(mixed $current, mixed $total, mixed $totalData, array $groups): array
    {
        return ['data' => [
            'product_groups' => $groups,
            'page' => [
                'current' => $current,
                'total' => $total,
                'total_data' => $totalData,
            ],
        ]];
    }

    protected function group(string $code, string $slug): array
    {
        return compact('code', 'slug');
    }
}

<?php

namespace App\Console\Commands;

use App\Models\CatalogItem;
use App\Services\Saweria\CatalogClassifier;
use App\Services\Saweria\SaweriaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class SyncSaweriaCatalog extends Command
{
    protected $signature = 'saweria:sync
                            {--prune : Tandai NONAKTIF item yang sudah tidak ada di Saweria}
                            {--dry-run : Tampilkan hasil tanpa menulis ke database}';

    protected $description = 'Tarik katalog game & produk dari toko top up Saweria';

    public function handle(SaweriaClient $client, CatalogClassifier $classifier)
    {
        if (! config('saweria.username')) {
            $this->error('SAWERIA_USERNAME belum diisi di .env');

            return self::FAILURE;
        }

        $this->info('Mengambil katalog dari Saweria...');

        try {
            $groups = $client->productGroups();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if (empty($groups)) {
            $this->error('Katalog kosong. Sinkronisasi dihentikan supaya data lama tidak terhapus.');

            return self::FAILURE;
        }

        $this->line(sprintf('  %d grup diterima', count($groups)));

        $dryRun = $this->option('dry-run');
        $seen = [];
        $created = 0;
        $updated = 0;

        // Posisi di daftar "Paling Laris"; kode yang tidak terdaftar bernilai null
        $bestSellers = array_flip(array_values(config('saweria.best_sellers', [])));

        foreach ($groups as $group) {
            if (empty($group['code']) || empty($group['slug'])) {
                continue;
            }

            [$type, $overridden] = $classifier->classify($group);
            $seen[] = $group['code'];

            if ($dryRun) {
                continue;
            }

            $item = CatalogItem::firstOrNew(['code' => $group['code']]);
            $exists = $item->exists;

            $item->fill([
                'external_id' => $group['id'] ?? null,
                'name' => $group['name'] ?? $group['code'],
                'slug' => $group['slug'],
                'publisher' => $group['publisher'] ?? null,
                'cover' => $group['cover'] ?? null,
                'type' => $type,
                'variant' => $group['variant'] ?? 'DIGITAL',
                'type_overridden' => $overridden,
                'status' => $group['status'] ?? 'ACTIVE',
                'featured_rank' => isset($bestSellers[$group['code']])
                    ? $bestSellers[$group['code']] + 1
                    : null,
                'synced_at' => Carbon::now(),
            ])->save();

            $exists ? $updated++ : $created++;
        }

        $pruned = 0;
        if ($this->option('prune') && ! $dryRun) {
            $pruned = CatalogItem::whereNotIn('code', $seen)
                ->where('status', 'ACTIVE')
                ->update(['status' => 'INACTIVE']);
        }

        $this->summarise($groups, $classifier, $created, $updated, $pruned, $dryRun);

        return self::SUCCESS;
    }

    protected function summarise(array $groups, CatalogClassifier $classifier, int $created, int $updated, int $pruned, bool $dryRun)
    {
        $games = 0;
        $products = 0;
        $variants = [];

        foreach ($groups as $group) {
            [$type] = $classifier->classify($group);
            $type === CatalogItem::TYPE_GAME ? $games++ : $products++;

            $variant = $group['variant'] ?? 'DIGITAL';
            $variants[$variant] = ($variants[$variant] ?? 0) + 1;
        }

        $this->newLine();
        $this->table(['Kelompok', 'Jumlah'], [
            ['Game', $games],
            ['Produk', $products],
            ['Total', $games + $products],
        ]);

        $rows = [];
        foreach ($variants as $variant => $count) {
            $rows[] = [$variant, $count];
        }
        $this->table(['Cara pemenuhan (dari Saweria)', 'Jumlah'], $rows);

        if ($dryRun) {
            $this->warn('Mode dry-run: tidak ada yang ditulis ke database.');

            return;
        }

        $this->info(sprintf('Tersimpan: %d baru, %d diperbarui%s.', $created, $updated,
            $pruned ? ", {$pruned} dinonaktifkan" : ''));
    }
}

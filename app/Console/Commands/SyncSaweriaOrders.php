<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Saweria\OrderRecorder;
use Illuminate\Console\Command;

/*
 * Jaring pengaman untuk pesanan yang detailnya belum lengkap.
 *
 * Dua keadaan yang ditangani di sini:
 *  - callback tiba sebelum Saweria selesai mencatat pesanannya, sehingga
 *    tracking-order belum mengenali id tersebut
 *  - endpoint Saweria sedang bermasalah saat callback diterima
 *
 * Selain itu, pesanan yang masih menunggu pembayaran atau sedang diproses
 * diperiksa ulang supaya statusnya mengikuti keadaan terkini.
 */
class SyncSaweriaOrders extends Command
{
    protected $signature = 'saweria:sync-orders {--limit=50 : Jumlah pesanan maksimum per jalan}';

    protected $description = 'Lengkapi detail dan perbarui status pesanan dari Saweria';

    public function handle(OrderRecorder $recorder)
    {
        $limit = max(1, (int) $this->option('limit'));

        $orders = Order::needsEnrichment()
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $refreshable = Order::whereNotNull('enriched_at')
            ->unsettled()
            ->orderBy('updated_at')
            ->limit(max(0, $limit - $orders->count()))
            ->get();

        $queue = $orders->concat($refreshable);

        if ($queue->isEmpty()) {
            $this->info('Tidak ada pesanan yang perlu diperbarui.');

            return self::SUCCESS;
        }

        $done = 0;
        $failed = 0;

        foreach ($queue as $order) {
            $recorder->enrich($order) ? $done++ : $failed++;
        }

        $this->info(sprintf('%d pesanan diperbarui, %d belum berhasil.', $done, $failed));

        return self::SUCCESS;
    }
}

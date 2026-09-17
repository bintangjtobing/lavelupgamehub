<?php

namespace App\Console\Commands;

use App\Services\GameStats\GameStatsManager;
use Illuminate\Console\Command;

/*
 * Menyegarkan statistik hero sebelum masa simpannya habis.
 *
 * Tanpa perintah ini, pengambilan data menumpang kunjungan pengguna: satu
 * pengunjung tiap jam menanggung waktu tunggu ke server Moonton, OpenDota,
 * rovmeta, dan Riot sekaligus. Dengan dijadwalkan lebih rapat daripada masa
 * simpannya, pengunjung selalu mendapat data yang sudah tersedia.
 */
class RefreshGameStats extends Command
{
    protected $signature = 'gamestats:refresh';

    protected $description = 'Ambil ulang statistik hero semua game ke penyimpanan sementara';

    public function handle(GameStatsManager $manager)
    {
        $games = array_keys(config('gamestats.games', []));

        if ($games === []) {
            $this->warn('Tidak ada game yang dikonfigurasi.');

            return self::SUCCESS;
        }

        $ok = 0;
        $failed = [];

        foreach ($games as $key) {
            $start = microtime(true);
            $success = $manager->refresh($key);
            $ms = (int) round((microtime(true) - $start) * 1000);

            $success ? $ok++ : $failed[] = $key;

            $this->line(sprintf('  %-6s %-7s %5d ms', $key, $success ? 'segar' : 'GAGAL', $ms));
        }

        $this->info(sprintf('%d dari %d game diperbarui.', $ok, count($games)));

        if ($failed) {
            // Sumber yang sedang bermasalah tidak dianggap kegagalan perintah:
            // data lamanya tetap dipakai sampai sumbernya pulih.
            $this->warn('Belum bisa dibaca: '.implode(', ', $failed));
        }

        return self::SUCCESS;
    }
}

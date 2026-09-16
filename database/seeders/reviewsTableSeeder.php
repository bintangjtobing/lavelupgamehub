<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
 * Data contoh untuk bagian ulasan.
 *
 * Semua baris di sini memakai email @example.com, dan itu bukan kebetulan:
 * testimonial.blade.php membaca akhiran tersebut untuk memasang label
 * "Contoh ulasan". Jadi jangan ganti domainnya, karena labelnya akan hilang
 * dan data contoh ini jadi tampak seperti ulasan pelanggan sungguhan.
 *
 * Ulasan asli yang masuk lewat form tidak tersentuh: seeder hanya menghapus
 * baris yang berdomain @example.com sebelum mengisi ulang.
 */
class reviewsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reviews')->where('email', 'like', '%@example.com')->delete();

        $rows = [];
        $date = Carbon::create(2026, 9, 12, 20, 15);

        foreach ($this->reviews() as $index => [$name, $message, $rating]) {
            $rows[] = [
                'name' => $name,
                'email' => 'contoh' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '@example.com',
                'phone' => '08' . str_pad((string) (11000000 + $index * 137), 10, '0', STR_PAD_LEFT),
                'message' => $message,
                'agree_terms' => true,
                'rating' => $rating,
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // Jarak antar ulasan dibuat tidak seragam supaya garis waktunya wajar
            $date = $date->copy()
                ->subHours(([7, 19, 31, 5, 44, 13, 26, 9, 52, 17])[$index % 10])
                ->subMinutes(([23, 7, 41, 12, 58, 34, 3, 49, 16, 27])[$index % 10]);
        }

        foreach (array_chunk($rows, 40) as $chunk) {
            DB::table('reviews')->insert($chunk);
        }
    }

    /**
     * [nama, pesan, rating]
     */
    protected function reviews(): array
    {
        return [
            ['Rizky Pratama', 'Beli diamond ML jam 2 pagi, masuk sebelum sempat nutup halaman. Mantap.', 5],
            ['Dwi Andini', 'Awalnya ragu karena baru pertama, ternyata aman. Lanjut langganan.', 5],
            ['Bagas S.', 'Bayar pakai QRIS, notif masuk langsung. Gak pakai nunggu lama.', 5],
            ['Nabila Putri', 'UC PUBG langsung masuk. Tinggal isi ID doang, gampang.', 5],
            ['Fajar Ramadhan', 'Prosesnya simpel, gak disuruh daftar akun dulu. Suka sih yang kayak gini.', 5],
            ['Intan Permata', 'Top up Valorant buat adik saya, lancar tanpa drama.', 5],
            ['Yoga Saputra', 'Pilihan nominalnya banyak, jadi bisa sesuai budget.', 4],
            ['Citra Ayu', 'Sempat salah masukin Zone ID, untung ketahuan sebelum bayar.', 4],
            ['Rian Hidayat', 'Udah tiga kali beli di sini, belum pernah bermasalah.', 5],
            ['Melati W.', 'Voucher Google Play kode langsung dikirim. Cepat.', 5],
            ['Dimas Prasetyo', 'Harga wajar, yang penting gak ribet.', 4],
            ['Ayu Lestari', 'Suka karena tampilannya bersih, nyari gamenya gampang.', 5],
            ['Hendra Gunawan', 'Free Fire masuk kurang dari semenit. Gercep.', 5],
            ['Sari Wulandari', 'Pakai DANA lancar, gak ada biaya aneh-aneh.', 5],
            ['Arif Budiman', 'Beli Steam Wallet buat sale kemarin, aman.', 5],
            ['Maya Anggraini', 'Fitur pencariannya membantu, gak perlu scroll panjang.', 5],
            ['Teguh Santoso', 'Sempat pending sekitar lima menit, tapi akhirnya masuk juga.', 4],
            ['Fitri Handayani', 'Diamond ML buat skin baru, prosesnya cepat banget.', 5],
            ['Galih Nugroho', 'Enak ada keterangan User ID-nya di mana, saya kan gaptek.', 5],
            ['Rara Safitri', 'Beli buat hadiah ulang tahun teman, langsung masuk ke akun dia.', 5],
            ['Bayu Kurniawan', 'Gak perlu install aplikasi apa-apa, tinggal buka web.', 5],
            ['Lina Marlina', 'Roblox buat anak saya, gampang dipahami prosesnya.', 5],
            ['Iqbal Maulana', 'Pilihan pembayarannya lengkap, saya pakai transfer bank.', 4],
            ['Dinda Aprilia', 'Sudah dua bulan langganan di sini buat top up mingguan.', 5],
            ['Reza Fahlevi', 'Weekly pass ML masuk tanpa kendala. Recommended.', 5],
            ['Putri Amelia', 'Gak nyangka secepat ini, saya kira harus nunggu jam kerja.', 5],
            ['Adit_99', 'Dari HP juga enak dipakai, gak berantakan tampilannya.', 5],
            ['Nanda Sitorus', 'Beli UC pas tengah malam, tetap masuk. Salut.', 5],
            ['Wahyu Setiawan', 'Sempat bingung awalnya, tapi setelah sekali coba jadi hafal.', 4],
            ['Tiara Novita', 'Jelas keterangan harganya, gak ada biaya tersembunyi.', 5],
            ['Firman Syah', 'Magic Chess masuk cepat, mantap.', 5],
            ['Anisa Rahma', 'Buat yang baru mulai main, ini gampang banget dipakai.', 5],
            ['Bima Sakti P.', 'Delta Force top up lancar, gak ada error.', 5],
            ['Lestari Ningsih', 'Saya suka karena gak dipaksa bikin akun dulu.', 5],
            ['Doni Kusuma', 'Sudah langganan, selalu masuk kurang dari dua menit.', 5],
            ['Vina Oktaviani', 'Voucher Steam buat kado, kodenya langsung terkirim.', 5],
            ['Hafiz Rahman', 'Pernah salah pilih nominal, untung bisa ulang dari awal.', 4],
            ['Selvi Agustina', 'Prosesnya jelas dari awal sampai selesai.', 5],
            ['Andre Wijaya', 'Top up Honor of Kings juga ada, jarang-jarang nih.', 5],
            ['Rossa Damayanti', 'Cepat dan gak ribet, sesuai yang dijanjikan.', 5],
            ['Ilham Akbar', 'Diamond FF masuk pas lagi mabar, langsung kepake.', 5],
            ['Kirana Dewi', 'Saya pakai GoPay, prosesnya mulus.', 5],
            ['Satria Yudha', 'Katalognya lengkap, game yang jarang pun ada.', 5],
            ['Ratna Sari', 'Beli buat suami, dia senang karena cepat masuknya.', 5],
            ['Bagus Pamungkas', 'Agak lama pas jam sibuk, tapi tetap masuk kok.', 4],
            ['Widya Astuti', 'Tampilannya enak dilihat, gak bikin pusing.', 5],
            ['Krisna Aditya', 'Top up Point Blank akhirnya ketemu juga di sini.', 5],
            ['Nurul Hikmah', 'Anak saya yang pilih gamenya, saya tinggal bayar. Praktis.', 5],
            ['Panji Nugraha', 'Sudah beberapa kali transaksi, semuanya lancar.', 5],
            ['Alya Zahra', 'Kode voucher langsung muncul, gak perlu nunggu email.', 5],
            ['Gilang Ramadhan', 'Buat yang males ke konter, ini solusinya.', 5],
            ['Fani Oktavia', 'Harganya masuk akal buat nominal kecil.', 4],
            ['Zaki Firdaus', 'Pertama kali beli online, ternyata gampang.', 5],
            ['Hesti Purnama', 'Top up Genshin belum ada, tapi yang lain lengkap.', 4],
            ['Rendi Saputro', 'Diamond masuk pas banget sebelum event berakhir.', 5],
            ['Cahya Ningrum', 'Saya suka ada pilihan kategori, jadi gampang nyarinya.', 5],
            ['Aldi Maulida', 'Transaksi lancar, saya pakai QRIS dari BCA.', 5],
            ['Mira Susanti', 'Beli dua kali dalam sehari, dua-duanya aman.', 5],
            ['Faisal Hakim', 'Enaknya gak usah nunggu admin bales chat.', 5],
            ['Devi Anggita', 'Simple, langsung to the point. Bagus.', 5],
            ['Rangga P.', 'Valorant Point masuk cepet, gak sampai lima menit.', 5],
            ['Salsa Bila', 'Awalnya takut penipuan, ternyata beneran masuk.', 5],
            ['Yudha Pratama', 'Sering top up di sini buat kebutuhan turnamen.', 5],
            ['Winda Lestari', 'Gampang dipakai walaupun saya bukan gamer.', 5],
            ['Oka Wirawan', 'Pilihan game-nya banyak, yang indie juga ada.', 5],
            ['Riska Amalia', 'Beli Spotify voucher juga bisa, gak cuma game.', 5],
            ['Tio Saputra', 'Masuk otomatis, gak perlu konfirmasi manual.', 5],
            ['Nadia Puspita', 'Sudah recommend ke teman-teman sekelas.', 5],
            ['Ariel Hutabarat', 'Sempat gagal sekali karena koneksi saya, diulang langsung bisa.', 4],
            ['Feby Ananda', 'Top up buat adik, dia langsung senang.', 5],
            ['Joko Prasetyo', 'Buat orang tua kayak saya pun gampang ngikutinnya.', 5],
            ['Indah Permata S.', 'Prosesnya rapi, ada keterangan di tiap langkah.', 5],
            ['Rafi Ardiansyah', 'Diamond ML murah-mahal tergantung nominal, tapi jelas listnya.', 4],
            ['Gita Savira', 'Suka karena gak banyak iklan mengganggu.', 5],
            ['Erlangga W.', 'PUBG UC masuk cepat, langsung bisa beli royale pass.', 5],
            ['Mega Utami', 'Beli pertama kali langsung berhasil, lega.', 5],
            ['Surya Darma', 'Cocok buat top up cepat pas lagi buru-buru.', 5],
            ['Tika Rahayu', 'Saya pakai di HP, tampilannya tetap rapi.', 5],
            ['Adnan Fauzi', 'Sempat ragu karena harga, tapi ternyata sepadan.', 4],
            ['Laras Kinanti', 'Voucher Google Play buat langganan aplikasi, lancar.', 5],
            ['Bobby Simanjuntak', 'Sudah jadi langganan bulanan saya.', 5],
            ['Sinta Nuraini', 'Anak saya minta top up, ternyata gampang diurus sendiri.', 5],
            ['Ega Pradana', 'Cepat, jelas, gak bertele-tele.', 5],
            ['Yuni Astari', 'Pilihan nominal kecil juga ada, cocok buat coba-coba.', 5],
            ['Rio Sanjaya', 'Top up AOV lancar, terima kasih.', 5],
            ['Dela Puspa', 'Pertama beli langsung masuk, gak pakai drama.', 5],
            ['Hafidz Ali', 'Kadang saya beli buat teman juga, semuanya aman.', 5],
            ['Novi Rahmawati', 'Enak bisa lihat harga dulu sebelum lanjut bayar.', 5],
            ['Pandu Wicaksono', 'Free Fire MAX masuk cepat, mantap.', 5],
            ['Ika Fitriani', 'Bagus, tapi semoga makin banyak pilihan pembayarannya.', 4],
            ['Rendra Saputra', 'Selalu masuk, belum pernah gagal sejauh ini.', 5],
            ['Ayla Kusuma', 'Saya beli buat hadiah, penerimanya senang.', 5],
            ['Toni Hermawan', 'Sudah coba beberapa situs, di sini paling simpel.', 5],
            ['Wulan Sari', 'Prosesnya cuma beberapa klik, suka.', 5],
            ['Bagja Nugraha', 'Top up Ragnarok akhirnya ada juga.', 5],
            ['Helena Sitanggang', 'Aman, masuk sesuai nominal yang dipilih.', 5],
            ['Irfan Maulana', 'Sempat pending agak lama, tapi tetap masuk. Gak rugi.', 3],
            ['Prita Dewanti', 'Saya suka karena keterangannya pakai bahasa Indonesia.', 5],
            ['Ganda Wibowo', 'Beli UC buat turnamen, prosesnya cepat.', 5],
            ['Ratih Kumala', 'Gampang dipahami, cocok buat pemula.', 5],
            ['Dicky Chandrawan', 'Sudah langganan sejak beberapa bulan lalu.', 5],
            ['Anggi Pratiwi', 'Top up buat dua akun sekaligus, dua-duanya masuk.', 5],
            ['Roni Sihombing', 'Pembayaran pakai VA juga bisa, membantu sekali.', 5],
            ['Utami Dewi', 'Cepat dan jelas, gak bikin was-was.', 5],
            ['Kevin Tanuwijaya', 'Steam Wallet masuk instan, langsung checkout game.', 5],
            ['Shinta Maharani', 'Suka karena gak perlu login dulu.', 5],
            ['Bram Setiadi', 'Masuk cepat walaupun beli di akhir pekan.', 5],
            ['Cut Rahma', 'Baru coba sekali, sejauh ini memuaskan.', 5],
            ['Agung Prayoga', 'Harga transparan, gak ada kejutan di akhir.', 5],
            ['Tari Meilani', 'Top up Zepeto juga ada, seneng banget.', 5],
            ['Hendri Saputra', 'Gak ribet, tinggal pilih dan bayar.', 5],
            ['Novita Sari', 'Saya rekomendasikan ke grup mabar.', 5],
            ['Ade Rahmat', 'Sempat salah nominal, jadi pelajaran buat lebih teliti.', 4],
            ['Zahra Aulia', 'Cepat masuknya, gak sampai satu menit.', 5],
            ['Fikri Haikal', 'Bagus buat top up rutin tiap minggu.', 5],
            ['Marsha Amanda', 'Suka tampilannya, gampang nyari game favorit.', 5],
            ['Yusuf Maulana', 'Beli diamond buat event, masuk tepat waktu.', 5],
            ['Karina Putri', 'Prosesnya aman, saya jadi tenang.', 5],
            ['Dedi Setiawan', 'Sudah beberapa kali, selalu lancar tanpa kendala.', 5],
            ['Ririn Anggraeni', 'Simple dan cepat, sesuai kebutuhan saya.', 5],
        ];
    }
}

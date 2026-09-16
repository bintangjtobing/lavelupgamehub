# Halaman detail Saweria

Alur: kartu katalog / hasil pencarian → `/topup/{slug}` → pilih nominal →
`https://saweria.co/{username}/toko-top-up/{slug}?item={product-slug}`.
ID pemain, data penerima, metode pembayaran, dan transaksi ditangani Saweria.

Harga berasal dari `pricing.selling_price` untuk produk ACTIVE dengan mata uang
IDR. `initiated_price` dapat kosong dan tidak dipakai sebagai harga jual. Detail
API ditarik setiap halaman dibuka, tanpa fallback harga lama. Halaman yang sudah
terbuka tidak melakukan polling; muat ulang untuk memperbarui harga. Biaya dan
harga akhir tetap mengikuti checkout Saweria.

HAR tanggal 16 September 2026 memperlihatkan kode halaman Saweria mencocokkan
`router.query.item` dengan `products[].slug`. Tidak ada token HAR yang diperlukan
untuk permintaan katalog publik. Jangan menambahkan HAR mentah ke repository.
Endpoint ini internal dan dapat berubah; jika gagal, halaman menawarkan muat
ulang atau membuka toko Saweria. Produk tidak aktif atau harga tidak valid tidak
ditampilkan.

Daftar game tetap berasal dari katalog lokal; jalankan `php artisan saweria:sync`
untuk memperbarui daftar game. Harga nominal tidak bergantung pada jadwal sync.
Konfigurasi timeout tersedia di `config/saweria.php`.
Permintaan halaman detail dibatasi 60 per menit per IP dan memakai satu percobaan
API (timeout bawaan 8 detik); sinkronisasi katalog tetap dapat mencoba ulang.

Verifikasi backend: `php artisan test --filter=ProductDetailTest`.
Tes memaksa SQLite `:memory:` di `createApplication()` sebelum migrasi tes berjalan.

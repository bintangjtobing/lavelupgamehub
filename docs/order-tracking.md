# Pelacakan pesanan

`GET /track-order` menampilkan formulir. `POST /track-order` memvalidasi UUID dan mengambil endpoint publik Saweria `/game-vouchers/tracking-order/{id}` di server. Tidak membuat pembayaran. Hasil HTML/JSON hanya memuat field tampilan yang diizinkan; tidak meneruskan kode voucher, data akun, URL pembayaran, atau payload mentah.

Track ID dikirim dalam body POST, tidak disimpan ke database/session oleh fitur ini, dan tidak dikirim ke analytics. Seluruh tracker dinonaktifkan pada halaman pelacakan. Respons lookup menggunakan `private, no-store` dan `noindex, nofollow`; GET formulir boleh diindeks. Konfigurasi proxy/APM eksternal tetap perlu tidak merekam body sensitif.

HAR yang diberikan memuat tiga request dengan selisih 11,875 dan 15,175 detik, semuanya sudah SUCCESS. Itu tidak membuktikan interval polling tetap 3–4 detik. Implementasi lokal memilih jeda 4 detik setelah respons untuk status belum final, berhenti pada selesai/gagal/kedaluwarsa/refund, menjeda saat tab tersembunyi, dan membatasi satu sesi polling hingga 10 menit. Gangguan sementara memakai jeda bertahap hingga 60 detik. Pengguna dapat mengecek ulang dengan tombol Perbarui status.

`amount_raw` adalah total, `game_voucher.selling_price` harga produk, dan `vendor_cut` biaya bila penjumlahan sesuai. `etc.amount_to_display` bukan total pesanan. `payment_updated_at` ditampilkan sebagai waktu pembayaran, bukan waktu pengiriman. Status di luar nilai yang dikenali ditampilkan netral; API publik ini belum merupakan kontrak resmi yang stabil.

Batas request: GET 60/menit, POST 120/menit per IP dan 20/menit per hash Track ID; koneksi upstream maksimal 3 detik dan request 8 detik. Pelacakan bukan bukti atribusi transaksi sehingga tidak menembakkan event GA4 `purchase`.

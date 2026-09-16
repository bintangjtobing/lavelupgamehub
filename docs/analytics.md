# GA4 LevelUp Market

Measurement ID: **G-ZMQ2EZKSBP**. Situs memakai Google tag langsung, satu kali per
halaman. Container lama GTM-NC65L328 juga berisi ID yang sama ketika diperiksa;
pemuat GTM telah dihapus dari template agar GA4 tidak dikonfigurasi dua kali.

Tracking aktif jika `ANALYTICS_ENABLED=true` (default saat APP_ENV=production)
dan host `levelupgamehub.com` atau `www.levelupgamehub.com`. Localhost dan host
staging tidak mengirim trafik uji. `.env.example` sengaja default false; set true
saat deployment produksi. Meta Pixel/Clarity yang sudah ada memakai batas host
yang sama dan dapat dimatikan dengan mengosongkan konfigurasi ID masing-masing.

| Event | Kapan / data |
| --- | --- |
| page_view | Google tag satu kali; URL tanpa query/fragment |
| view_item_list | Kartu benar-benar terlihat; daftar catalog/popular |
| select_item | Game dari katalog/pencarian, atau nominal dipilih |
| view_item | Halaman detail grup game/voucher dibuka |
| begin_checkout | Klik lanjut ke Saweria; ID game stabil, ID paket Saweria, varian nominal, currency IDR, value harga paket |
| catalog_search | Hasil pencarian; hanya nama katalog yang cocok, bukan teks mentah pengunjung |
| catalog_filter | Kategori tab yang dipilih |
| catalog_load_more | Tombol tampilkan lebih banyak |
| contact_click | WhatsApp/Facebook/Instagram/email/telepon; tanpa isi pesan |
| generate_lead | Form kontak berhasil diproses server |
| review_submit | Review berhasil disimpan server |

Harga pada begin_checkout adalah harga paket sebelum biaya Saweria. Tidak ada
`purchase` palsu: klik keluar bukan bukti pembayaran. Pendapatan/transaksi lunas
memerlukan webhook Saweria terverifikasi dan rancangan atribusi server terpisah.
Jangan menyimpulkan conversion rate pembelian dari begin_checkout.

Custom event tidak mengirim nama pengunjung, email, nomor telepon, user ID game,
isi pesan, atau teks review. Query dan fragment URL dibuang dari konfigurasi
page_view. catalog_search memakai label hasil katalog sehingga tidak menjadi log teks
bebas. Tidak ada User-ID atau enhanced conversions yang diaktifkan.

## Pemeriksaan di akun GA4 setelah deployment

1. Pastikan stream web memakai G-ZMQ2EZKSBP dan tidak dikonfigurasi ulang lewat
   container/tag lain. Di Enhanced measurement nonaktifkan Form interactions
   dan Site search jika ingin hanya memakai event manual di atas.
2. Aktifkan `ANALYTICS_DEBUG=true` sementara untuk DebugView, lalu kembalikan false.
3. Cek page_view, view_item, select_item dan begin_checkout di DebugView/Realtime.
4. Daftarkan custom dimensions event-scoped: checkout_provider, search_source,
   category, contact_method, form_id, matched_items; serta custom metric result_count bila perlu.
   Ecommerce items/currency/value memakai parameter standar GA4.
   `saweria_product_id` dapat didaftarkan sebagai custom dimension item-scoped;
   `item_id` tetap slug game sepanjang funnel dan nominal ada di `item_variant`.
5. Tandai generate_lead atau begin_checkout sebagai key event sesuai tujuan bisnis;
   begin_checkout tetap hanya niat melanjutkan pembayaran.

Hak akses akun GA4 belum digunakan pada pekerjaan lokal ini. Tes membuktikan
konfigurasi dan payload browser, bukan konfirmasi data sudah masuk dashboard.

Referensi: [Ecommerce GA4](https://developers.google.com/analytics/devguides/collection/ga4/ecommerce),
[Recommended events](https://developers.google.com/analytics/devguides/collection/ga4/reference/events).

Meta Pixel dan Clarity tidak dimuat jika URL mengandung query atau fragment, agar
parameter arbitrer tidak ikut terkirim ke layanan tersebut.

UTM source/medium/campaign/id/content/term yang berbentuk label kampanye pendek
(huruf, angka, titik, garis bawah, tanda hubung; maksimum 80 karakter) dipetakan
ke parameter campaign GA4 sebelum query URL dibuang. Email, spasi, dan deretan
angka panjang ditolak. Jangan memakai data pribadi sebagai label kampanye.

Halaman `/track-order` menonaktifkan semua tracker, termasuk pada hasil POST dan polling. Track ID dan status pesanan tidak dikirim ke GA4. Event `purchase` memerlukan transaksi terverifikasi dan deduplikasi di integrasi terpisah.

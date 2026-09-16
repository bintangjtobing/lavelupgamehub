# Riset kata kunci LevelUp Market

Tanggal pengambilan sumber: **16 September 2026**
Target bahasa dan wilayah: **Bahasa Indonesia, Indonesia**

## Ringkasan keputusan

Peta implementasi berada di `config/seo_keywords.php`. Kata kunci primer dipilih sebagai frasa yang paling dekat
dengan tujuan masing-masing halaman. Kata kunci sekunder menangkap variasi nama, singkatan, mata uang game, dan maksud
pencarian seperti `harga` atau `cara top up`.

Label “primer” dalam dokumen ini bukan klaim volume pencarian tertinggi atau tingkat persaingan terbaik. Proyek ini
tidak memiliki data Google Search Console, Google Ads Keyword Planner, atau alat SEO berbayar. Karena itu, penelitian
ini menghasilkan hipotesis kata kunci yang relevan berdasarkan fungsi halaman, katalog aktif, dan istilah yang dipakai
oleh penerbit atau halaman top-up Indonesia. Dampaknya perlu divalidasi setelah tayang melalui data kueri dan halaman di
Search Console.

Google menyatakan bahwa tag `<meta name="keywords">` tidak digunakan untuk pengindeksan atau pemeringkatan. Daftar
kata kunci tetap disediakan sebagai kamus kompatibilitas dan panduan editorial untuk judul, deskripsi, heading, tautan,
serta copy yang terbaca pengguna. Daftar ini tidak perlu dicetak sebagai blok teks atau diulang pada halaman.

## Metodologi

1. Menginventarisasi katalog lokal dengan kueri baca-saja. Hasil saat penelitian: **110 produk aktif**. Sepuluh produk
   dengan `featured_rank` menjadi prioritas override; urutan tersebut adalah kurasi manual aplikasi, bukan data
   penjualan.
2. Memeriksa nama, slug, publisher, kategori, dan cara pemenuhan pada `catalog_items`. Nama publisher dipakai untuk
   verifikasi identitas produk, bukan sebagai klaim bahwa LevelUp merupakan situs publisher atau mitra resmi.
3. Memeriksa dokumentasi Google Search untuk penerapan keyword yang wajar: judul unik dan ringkas, deskripsi khusus
   halaman, bahasa yang sama dengan isi utama, serta larangan keyword stuffing.
4. Membandingkan istilah Indonesia pada sumber milik penerbit, pusat bantuan resmi, dan halaman produk milik Codashop
   atau UniPin. Penelitian mengambil istilah produknya saja dan tidak menyalin promosi, harga, jaminan pengiriman, atau
   klaim resmi milik penjual lain.
5. Memisahkan top up langsung dari voucher. Contohnya, halaman Roblox lokal adalah produk voucher, sehingga copy tidak
   menjanjikan Robux masuk langsung. FolaPlay diperlakukan sebagai voucher atau paket akses, sedangkan Steam dan Google
   Play diperlakukan sebagai kode voucher.
6. Menyediakan fallback berdasarkan kategori dengan placeholder `{name}`. Dengan demikian seluruh 110 produk mendapat
   judul dan deskripsi yang memakai nama katalog aktual tanpa mengarang mata uang, denominasi, metode pembayaran, atau
   waktu pemrosesan.

## Prinsip Google Search yang diterapkan

- Google mengabaikan meta keywords. Fokus implementasi seharusnya pada judul, deskripsi, heading, dan copy yang membantu
  pengguna.
- Judul harus deskriptif, ringkas, berbeda antarhalaman, dan tidak mengulang variasi keyword secara berlebihan.
- Google terutama membentuk snippet dari isi halaman dan kadang memakai meta description. Deskripsi harus unik,
  manusiawi, dan benar-benar menjelaskan halaman.
- Deskripsi terprogram diperbolehkan untuk situs berbasis basis data selama memakai data khusus halaman dan tetap mudah
  dibaca.
- Keyword stuffing dan halaman massal yang tidak memberi nilai kepada pengguna berisiko melanggar kebijakan spam.

## Peta halaman statis

| Path | Kata kunci primer | Maksud halaman | Kata kunci sekunder utama |
| --- | --- | --- | --- |
| `/` | top up game dan voucher digital | Menemukan kategori dan produk dari halaman depan | top up game Indonesia; voucher game Indonesia; katalog produk digital; harga top up game |
| `/topup` | katalog top up game | Menelusuri dan membandingkan produk aktif | daftar top up game; voucher game; harga voucher digital; cari top up game |
| `/about` | tentang LevelUp Market | Memahami fungsi situs dan alur checkout | LevelUp Gaming Market; katalog LevelUp Market; cara kerja LevelUp Market |
| `/contact` | hubungi LevelUp Market | Meminta bantuan katalog atau penggunaan situs | kontak LevelUp Market; bantuan top up game; bantuan voucher digital |
| `/faq` | cara top up game | Mendapat jawaban sebelum memilih produk dan checkout | pertanyaan top up game; cara beli voucher game; cara checkout Saweria; bantuan top up game |
| `/privacy-policy` | kebijakan privasi LevelUp Market | Memahami pemrosesan data, cookie, analitik, dan layanan terpisah | data pengguna LevelUp Market; cookie LevelUp Market; privasi formulir kontak |
| `/terms-and-conditions` | syarat dan ketentuan LevelUp Market | Membaca aturan penggunaan katalog dan checkout | syarat penggunaan LevelUp Market; ketentuan katalog top up; ketentuan checkout Saweria |
| `/track-order` | lacak pesanan top up | Memeriksa pesanan dengan Track ID Saweria | cek status pesanan top up; lacak pesanan Saweria; cek Track ID Saweria; status voucher game |

Kata kunci halaman bantuan dan legal sengaja berfokus pada nama halaman serta merek. Memasukkan daftar nama game pada
halaman tersebut tidak menambah nilai dan dapat mengaburkan maksud pencarian.

## Sepuluh produk prioritas

| Slug | Publisher pada katalog | Kata kunci primer | Alias dan istilah sekunder | Batasan copy |
| --- | --- | --- | --- | --- |
| `mobile-legends-bang-bang` | Moonton | top up Mobile Legends | top up ML; top up MLBB; Diamond Mobile Legends; Diamond MLBB; harga diamond ML | Gunakan User ID dan Zone ID hanya sebagai petunjuk data; jangan menjanjikan pengiriman instan. |
| `free-fire` | Garena | top up Free Fire | top up FF; Diamond Free Fire; Diamond FF; harga diamond FF; cara top up Free Fire | Diamond adalah istilah resmi dalam game; hindari klaim “termurah” atau jaminan kecepatan. |
| `pubg-mobile` | Tencent Games | top up PUBG Mobile | UC PUBG Mobile; beli UC PUBG Mobile; harga UC PUBG; top up UC PUBG | UC adalah mata uang produk; jangan menyamakan klik checkout dengan transaksi berhasil. |
| `folaplay` | PT FOLAGO DIGITAL MEDIA | voucher FolaPlay | beli voucher FolaPlay; paket FolaPlay; langganan FolaPlay; cara redeem voucher FolaPlay | Paket dan masa akses dapat berubah. Jangan mengunci copy pada satu pertandingan, kompetisi, atau promo sementara. |
| `valorant` | Riot Games | top up VALORANT | VALORANT Points; VP VALORANT; beli VALORANT Points; harga VP VALORANT | VP adalah singkatan yang lazim. Hindari menyebut nominal tetap karena katalog dapat berubah. |
| `magic-chess-go-go` | Vizta Games | top up Magic Chess Go Go | Diamond Magic Chess Go Go; top up MCGG; Diamond MCGG; harga Diamond Magic Chess | Codashop dan UniPin sama-sama memakai Diamond dan User ID/Zone ID. Jangan menyalin harga atau promo mereka. |
| `delta-force-garena` | Garena | top up Delta Force Garena | Delta Coins Garena; top up Delta Coins; harga Delta Coins; Delta Force Indonesia | Situs Garena membedakan Delta Coins dan Delta Tickets. Copy LevelUp tidak boleh menjanjikan jenis paket yang tidak terlihat pada katalog aktif. |
| `roblox` | Roblox Corporation | voucher Roblox | Roblox Gift Card; beli gift card Roblox; Roblox Credit; cara redeem voucher Roblox; cara beli Robux dengan gift card | Produk lokal adalah voucher. Sebagian kode menjadi Roblox Credit dan jenis tertentu dapat menjadi Robux; jangan menyebutnya top up Robux langsung. |
| `steam-voucher-indonesia` | Steam | voucher Steam Indonesia | Steam Wallet Indonesia; kode Steam Wallet; gift card Steam; cara redeem Steam Wallet | Jelaskan kode menambah saldo setelah ditukarkan dan ingatkan kecocokan wilayah atau mata uang; jangan menyatakan kredit langsung sebelum redeem. |
| `google-play-indonesia` | Google | voucher Google Play Indonesia | kode voucher Google Play; saldo Google Play; gift card Google Play Indonesia; cara redeem voucher Google Play | Nilai kode menambah Saldo Google Play setelah redeem. Negara akun harus sesuai; jangan menganggap produk sebagai top up akun langsung. |

Nama publisher dan nama game harus tetap dibedakan. Misalnya, Moonton adalah publisher pada data Mobile Legends,
sementara LevelUp adalah katalog dan Saweria menangani checkout. Nama Codashop dan UniPin hanya digunakan sebagai sumber
terminologi penelitian dan tidak boleh dimasukkan ke metadata LevelUp.

## Cakupan 110 produk

`product_fallback` memiliki template terpisah untuk kategori `game`, `voucher`, `entertainment`, dan `default`.
Integrasi harus:

1. mencari override berdasarkan `$item->slug`;
2. jika tidak ada, memilih fallback berdasarkan `$item->category`;
3. mengganti `{name}` hanya dengan `$item->name` yang tersimpan;
4. memakai fallback `default` bila kategori tidak dikenal;
5. tidak membentuk alias, mata uang, denominasi, atau publisher dari slug.

Pendekatan ini menutup seluruh katalog aktif tanpa memelihara 110 blok copy yang cepat kedaluwarsa. Override baru hanya
perlu ditambahkan ketika ada sumber yang cukup untuk memastikan istilah khusus produk. Search Console nantinya dapat
menunjukkan produk mana yang layak mendapat override berikutnya.

## Rencana pengukuran setelah rilis

1. Tunggu data kueri dan halaman di Search Console; jangan menilai perubahan dari peringkat manual satu perangkat.
2. Bandingkan klik, impresi, CTR, dan posisi rata-rata per halaman selama periode yang sebanding.
3. Kelompokkan kueri bermerek, kategori umum, dan produk. Jangan menganggap impresi sebagai transaksi.
4. Tambahkan alias hanya bila relevan dengan produk dan copy tetap terbaca alami.
5. Perbarui istilah ketika nama produk atau cara pemenuhan di katalog berubah.

## Sumber

Semua sumber berikut diakses pada **16 September 2026**.

### Google Search

- [Google Search Central: meta tags yang didukung](https://developers.google.com/search/docs/crawling-indexing/special-tags) — menegaskan meta keywords diabaikan dan meta description dapat dipakai sebagai snippet.
- [Google Search Central: title links](https://developers.google.com/search/docs/appearance/title-link) — judul deskriptif, ringkas, unik, dan tidak penuh pengulangan keyword.
- [Google Search Central: snippets dan meta descriptions](https://developers.google.com/search/docs/appearance/snippet) — deskripsi unik serta pembuatan deskripsi terprogram untuk situs basis data.
- [Google Search Central: SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide) — fokus pada isi yang membantu pengguna dan menghindari keyword stuffing.
- [Google Search Central: spam policies](https://developers.google.com/search/docs/essentials/spam-policies) — batas keyword stuffing dan scaled content tanpa nilai tambah.

### Produk dan istilah

- [Codashop Indonesia: Mobile Legends: Bang Bang](https://www.codashop.com/id-id/mobile-legends) — penggunaan Mobile Legends, ML, Diamond, User ID, dan Zone ID dalam konteks Indonesia.
- [Garena Free Fire Support: mata uang Diamond](https://ff.garena.com/en/article/180/) dan [UniPin Indonesia: Free Fire](https://www.unipin.com/id/garena/free-fire) — validasi Diamond Free Fire, Player ID, dan istilah top up Indonesia.
- [Midasbuy Indonesia: PUBG Mobile](https://www.midasbuy.com/midasbuy/id/redeem/pubgm?lang=id) — penggunaan PUBG Mobile, UC, dan Player ID oleh toko isi ulang Tencent.
- [UniPin Indonesia: FolaPlay](https://www.unipin.com/id/game/folaplay) — FolaPlay sebagai platform streaming dengan produk berbentuk paket atau voucher.
- [Riot Games VALORANT Support: panduan toko](https://support-valorant.riotgames.com/hc/ar/articles/360046053753) dan [Codashop Indonesia: VALORANT](https://www.codashop.com/id-id/valorant-mandiri) — validasi VALORANT Points dan VP serta penggunaan istilah Indonesia.
- [Codashop Indonesia: Magic Chess: Go Go](https://www.codashop.com/id-id/magic-chess-go-go) dan [UniPin Indonesia: Magic Chess: Go Go](https://api-cluster.unipin.com/id/magic-chess-gogo) — validasi Diamond, MCGG, User ID, dan Zone ID.
- [Garena Delta Force: pembaruan top up](https://deltaforce.garena.com/en/news/announcement/QJ3BDJ) — pembedaan Delta Coins yang diperoleh melalui top up dan Delta Tickets.
- [Roblox Support: redeem dan menggunakan Gift Card](https://en.help.roblox.com/hc/en-us/articles/115005566223-How-to-redeem-and-spend-your-Gift-Card) dan [Codashop Indonesia: Roblox Gift Cards](https://www.codashop.com/id-id/roblox-gift-cards) — pembedaan Gift Card, Roblox Credit, dan Robux.
- [Steam: Gift Cards](https://store.steampowered.com/digitalgiftcards/Digital) dan [Codashop Indonesia: Steam Wallet Code](https://www.codashop.com/id-id/steam-wallet-code-sea) — kode atau gift card digunakan untuk menambah Steam Wallet setelah redeem.
- [Google Play Help: menambahkan Saldo Google Play](https://support.google.com/googleplay/answer/3423011?hl=id) dan [daftar negara serta denominasi gift card](https://support.google.com/googleplay/answer/3422734) — voucher menambah saldo setelah redeem dan produk Indonesia memiliki denominasi IDR.

Harga, diskon, metode pembayaran, dan klaim layanan milik situs sumber sengaja tidak dipindahkan ke copy LevelUp karena
dapat berubah dan tidak membuktikan layanan LevelUp memiliki atribut yang sama.

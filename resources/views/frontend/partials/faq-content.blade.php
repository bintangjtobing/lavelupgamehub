<section class="faq-section ptb-120" aria-labelledby="faq-title">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <header class="section-header text-center">
                    <h1 class="section-title" id="faq-title">Pertanyaan Umum LevelUp Market</h1>
                    <p>Temukan penjelasan tentang katalog, checkout Saweria, data yang perlu disiapkan, dan cara
                        menghubungi LevelUp.</p>
                </header>
                <div class="row justify-content-center">
                    @php
                        $faqs = [
                            ['Apa itu LevelUp Market?', 'LevelUp Market adalah situs katalog untuk mencari game, voucher, dan produk hiburan digital. Kamu dapat melihat produk dan pilihan paket di situs ini, kemudian melanjutkan checkout melalui Saweria.'],
                            ['Bagaimana cara melakukan top up?', 'Buka katalog, cari produk, lalu pilih halaman detailnya. Tentukan paket yang tersedia dan ikuti tautan checkout ke Saweria. Di sana, isi data yang diminta untuk produk tersebut, periksa pesanan, dan selesaikan pembayaran.'],
                            ['Mengapa checkout dilakukan di Saweria?', 'LevelUp menampilkan katalog dan detail produk, sedangkan pengisian data pesanan serta pembayaran dilakukan pada halaman Saweria. Saat tautan dibuka, periksa kembali alamat halaman, nama produk, nominal, dan data akun sebelum melanjutkan.'],
                            ['Metode pembayaran apa yang tersedia?', 'Metode yang dapat digunakan ditampilkan oleh Saweria pada saat checkout dan dapat berubah. Gunakan pilihan yang tersedia pada halaman pembayaran untuk pesananmu.'],
                            ['Berapa lama pesanan diproses?', 'Waktu pemrosesan bergantung pada produk dan informasi yang ditampilkan saat checkout. Pastikan data akun benar dan simpan informasi transaksi. Jika ada kendala, hubungi LevelUp dengan menyebutkan produk dan detail masalah tanpa mengirim kata sandi akun.'],
                            ['Bagaimana cara melacak pesanan?', 'Buka menu Lacak Pesanan dan masukkan Track ID dari Saweria. Status pembayaran dan pengiriman diperbarui otomatis selama pesanan belum selesai dan halaman aktif. Simpan Track ID untuk dirimu sendiri.'],
                            ['Bagaimana mengetahui harga terbaru?', 'Harga paket yang tersedia ditampilkan pada halaman detail produk. Karena katalog dapat diperbarui, gunakan harga yang terlihat saat kamu membuka produk dan periksa kembali nominal di Saweria sebelum membayar.'],
                            ['Data apa yang perlu saya isi?', 'Kebutuhan data berbeda untuk setiap produk. Halaman detail akan menampilkan petunjuk yang tersedia, dan formulir checkout Saweria menunjukkan kolom yang harus diisi. Jangan pernah mengirim kata sandi akun game melalui formulir kontak.'],
                            ['Bagaimana cara mencari produk?', 'Gunakan kolom pencarian untuk mengetik nama game atau voucher. Kamu juga dapat menyaring katalog berdasarkan game, voucher, produk hiburan, atau produk yang baru ditambahkan ke katalog.'],
                            ['Bagaimana LevelUp menggunakan data saya?', 'Formulir kontak mengumpulkan nama, email, dan pesan agar pertanyaan dapat diterima dan dijawab. Penjelasan lain mengenai cookie, analitik, ulasan publik, dan tautan ke Saweria tersedia di halaman Kebijakan Privasi.'],
                            ['Bagaimana cara menghubungi LevelUp?', 'Gunakan formulir kontak, email help@levelupgamehub.com, atau WhatsApp yang tercantum pada halaman kontak.'],
                        ];
                    @endphp
                    @foreach ($faqs as [$question, $answer])
                        <div class="col-xl-12 col-lg-12 mb-20">
                            <div class="faq-wrapper">
                                <div class="faq-item">
                                    <h2 class="faq-title"><span class="title">{{ $question }}</span><span
                                            class="right-icon" aria-hidden="true"></span></h2>
                                    <div class="faq-content" style="display: none;">
                                        <p>{{ $answer }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-center mt-30">Belum menemukan jawaban? <a href="/contact"
                        title="Buka halaman kontak LevelUp Market">Hubungi LevelUp Market</a>.</p>
            </div>
        </div>
    </div>
</section>

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class reviewsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reviews')->insert([
            [
                'name' => 'Anonymous',
                'email' => 'user1234@example.com',
                'phone' => '081234567890',
                'message' => 'Bakal sering beli CP di sini. Kalau beli langsung di store COD mahal, jadi lebih baik di sini!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 9, rand(1, 30)),
                'updated_at' => Carbon::create(2024, 9, rand(1, 30)),
            ],
            [
                'name' => 'Anonymous',
                'email' => 'user5678@example.com',
                'phone' => '082134567891',
                'message' => 'Pelayanan cepat, langsung masuk setelah transfer. Recommended banget!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 10, rand(1, 31)),
                'updated_at' => Carbon::create(2024, 10, rand(1, 31)),
            ],
            [
                'name' => 'Heri S.',
                'email' => 'heri@example.com',
                'phone' => '083134567892',
                'message' => 'Langsung masuk dan cepat. Makasih banyak, bakal jadi langganan!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 10, rand(1, 31)),
                'updated_at' => Carbon::create(2024, 10, rand(1, 31)),
            ],
            [
                'name' => 'Ravan N.',
                'email' => 'ravan123@example.com',
                'phone' => '084134567893',
                'message' => 'Terima kasih, proses cepat dan bisa dipercaya. Pasti top up lagi di sini!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 11, rand(1, 6)),
                'updated_at' => Carbon::create(2024, 11, rand(1, 6)),
            ],
            // Additional generic reviews
            [
                'name' => 'Dewi P.',
                'email' => 'dewi123@example.com',
                'phone' => '085134567894',
                'message' => 'Pengalaman top up di sini sangat memuaskan. Harga bersaing dan respons cepat!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 9, rand(1, 30)),
                'updated_at' => Carbon::create(2024, 9, rand(1, 30)),
            ],
            [
                'name' => 'Andi T.',
                'email' => 'andi123@example.com',
                'phone' => '086134567895',
                'message' => 'Prosesnya cepat dan customer service ramah. Top banget!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 10, rand(1, 31)),
                'updated_at' => Carbon::create(2024, 10, rand(1, 31)),
            ],
            [
                'name' => 'Siti R.',
                'email' => 'siti123@example.com',
                'phone' => '087134567896',
                'message' => 'Harga lebih murah dibanding yang lain, recommended buat para gamer!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 10, rand(1, 31)),
                'updated_at' => Carbon::create(2024, 10, rand(1, 31)),
            ],
            [
                'name' => 'Budi S.',
                'email' => 'budi123@example.com',
                'phone' => '088134567897',
                'message' => 'Top up di sini cepet banget, nggak sampai 5 menit udah masuk. Mantap!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 10, rand(1, 31)),
                'updated_at' => Carbon::create(2024, 10, rand(1, 31)),
            ],
            [
                'name' => 'Rina K.',
                'email' => 'rina123@example.com',
                'phone' => '089134567898',
                'message' => 'Awalnya ragu, tapi setelah coba ternyata aman dan terpercaya. Thanks!',
                'agree_terms' => true,
                'rating' => 5,
                'created_at' => Carbon::create(2024, 11, rand(1, 6)),
                'updated_at' => Carbon::create(2024, 11, rand(1, 6)),
            ],
        ]);
    }
}
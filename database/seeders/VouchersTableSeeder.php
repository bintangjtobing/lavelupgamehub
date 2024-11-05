<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VouchersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('vouchers')->insert([
            [
                'name' => 'Voucher Google Play',
                'slug' => 'google-play',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/b8f7992df097dd42d63df97bd90d15b5.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/b8f7992df097dd42d63df97bd90d15b5.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/google-play',
            ],
            [
                'name' => 'Steam Wallet',
                'slug' => 'steam',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/6c6310bc82c3e423f5fabd0e719a7d75.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/6c6310bc82c3e423f5fabd0e719a7d75.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/steam',
            ],
            [
                'name' => 'Roblox',
                'slug' => 'roblox',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/bb8bcd8768e67ca829ec31ef5a14c4bc.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/bb8bcd8768e67ca829ec31ef5a14c4bc.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/roblox',
            ],
            [
                'name' => 'Minecraft',
                'slug' => 'minecraft',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/0f9d9f1404ecb25f44bf0c5ec289be60.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/0f9d9f1404ecb25f44bf0c5ec289be60.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/minecraft',
            ],
            [
                'name' => 'Point Blank Cash',
                'slug' => 'point-blank',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/4d28dddb44768d23195895237c790ac7.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/4d28dddb44768d23195895237c790ac7.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/point-blank',
            ],
            [
                'name' => 'PSN Money Voucher (ID)',
                'slug' => 'psn',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/a0a7c77fa94f422b784fb9f72a46d039.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/a0a7c77fa94f422b784fb9f72a46d039.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/psn',
            ],
            [
                'name' => 'Nintendo eShop',
                'slug' => 'nintendo-eshop',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/282e232b845bf03015ba700b629b78a3.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/282e232b845bf03015ba700b629b78a3.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/nintendo-eshop',
            ],
            [
                'name' => 'Garena Shell',
                'slug' => 'garena-shell',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/0c37cb532949590d0a8aa84d268408b8.png',
                'data_src' => 'https://semutganteng.fra1.digitaloceanspaces.com/UniPlay/0c37cb532949590d0a8aa84d268408b8.png',
                'voucher_url' => 'https://topup.levelupgamehub.com/voucher/garena-shell',
            ],
        ]);
    }
}
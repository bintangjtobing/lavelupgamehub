<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/*
 * Katalog sekarang seluruhnya berasal dari tabel catalog_items yang diisi
 * perintah saweria:sync. Dua tabel lama ini diisi seeder manual dan sudah
 * tidak dibaca kode mana pun.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('games');
        Schema::dropIfExists('vouchers');
    }

    public function down()
    {
        // Sengaja dikosongkan: data lamanya tidak bisa dipulihkan dari sini.
        // Kalau perlu, ambil seeder-nya dari commit 79ba413.
    }
};

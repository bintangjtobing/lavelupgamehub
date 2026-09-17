<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Bot tidak dihapus, hanya ditandai.
 *
 * Laporan menyaringnya secara bawaan supaya angka konversi tidak melenceng,
 * tetapi barisnya tetap disimpan sebagai bukti saat menelusuri lonjakan
 * trafik yang mencurigakan.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('visitor_sessions', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->index()->after('device');
            $table->string('bot_name', 64)->nullable()->after('is_bot');
        });
    }

    public function down()
    {
        Schema::table('visitor_sessions', function (Blueprint $table) {
            $table->dropColumn(['is_bot', 'bot_name']);
        });
    }
};

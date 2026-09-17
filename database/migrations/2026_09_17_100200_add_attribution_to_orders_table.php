<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Menyambungkan pesanan ke kunjungan yang memicunya.
 *
 * Sambungan ini tidak bisa pasti. Pembayaran terjadi di halaman Saweria, dan
 * callback yang kembali tidak membawa penanda sesi kita. Jadi pencocokan
 * dilakukan berdasarkan produk yang sama dan selisih waktu yang wajar dengan
 * penekanan tombol checkout terakhir. Karena itu hasilnya disimpan bersama
 * tingkat keyakinannya, bukan sebagai fakta.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('session_id')->nullable()->after('saweria_id')->index();
            $table->string('attribution', 16)->nullable()->after('session_id');
            $table->string('utm_source')->nullable()->after('attribution')->index();
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'attribution', 'utm_source', 'utm_medium', 'utm_campaign']);
        });
    }
};

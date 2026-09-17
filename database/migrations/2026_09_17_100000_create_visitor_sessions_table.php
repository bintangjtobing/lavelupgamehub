<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Satu baris per kunjungan, dikenali lewat cookie acak milik situs ini sendiri.
 *
 * Yang sengaja TIDAK disimpan: alamat IP dan user agent mentah. Untuk mengukur
 * funnel, keduanya tidak diperlukan; yang dibutuhkan hanya asal trafik dan
 * jenis perangkat.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Sumber trafik disimpan dari kunjungan PERTAMA (first-touch), supaya
            // kredit tidak berpindah saat pengunjung kembali lewat tautan lain.
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            $table->string('referrer_host')->nullable()->index();
            $table->string('landing_path')->nullable();
            $table->string('device', 16)->nullable();

            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('product_views')->default(0);
            $table->unsignedInteger('checkout_clicks')->default(0);

            $table->timestamp('started_at')->index();
            $table->timestamp('last_seen_at')->index();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_sessions');
    }
};

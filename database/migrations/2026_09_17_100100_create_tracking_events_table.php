<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Rekam jejak langkah pengunjung: melihat halaman, membuka produk, menekan
 * tombol checkout, sampai pesanannya masuk lewat webhook.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();

            $table->uuid('session_id')->nullable()->index();
            $table->string('name', 32)->index();
            $table->string('path')->nullable();

            // Diisi untuk peristiwa yang menyangkut satu produk
            $table->string('item_slug')->nullable()->index();
            $table->string('product_slug')->nullable();
            $table->unsignedBigInteger('value')->nullable();

            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->index();

            $table->timestamps();

            $table->index(['name', 'occurred_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracking_events');
    }
};

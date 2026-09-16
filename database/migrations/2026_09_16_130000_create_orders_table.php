<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Id pesanan dari Saweria, sekaligus Track ID yang dipegang pembeli.
            // Unik supaya callback yang dikirim berulang tidak menggandakan baris.
            $table->uuid('saweria_id')->unique();

            // Dari payload webhook
            $table->string('donator_name')->nullable();
            $table->string('donator_email')->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('amount_raw')->nullable();

            // Dari endpoint tracking-order
            $table->string('game_name')->nullable();
            $table->string('product_name')->nullable();
            $table->string('cover')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('state')->default('unknown')->index();
            $table->string('payment_status')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->unsignedBigInteger('product_price')->nullable();
            $table->unsignedBigInteger('fee')->nullable();
            $table->string('currency', 8)->nullable();

            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Kapan detail produk berhasil diambil. NULL berarti masih perlu dilengkapi.
            $table->timestamp('enriched_at')->nullable()->index();
            $table->unsignedSmallInteger('enrich_attempts')->default(0);

            // Isi callback apa adanya, untuk penelusuran bila bentuknya berubah
            $table->json('payload')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

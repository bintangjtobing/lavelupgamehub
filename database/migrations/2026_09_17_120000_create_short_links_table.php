<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();

            // Bagian setelah /s/ pada tautan. Disimpan apa adanya dan dibandingkan
            // persis, jadi huruf besar-kecil dibedakan.
            $table->string('code', 40)->unique();

            $table->string('label');                    // nama yang dikenali pengelola
            $table->string('template', 40)->nullable(); // template asal, untuk pengelompokan
            $table->text('target');                     // tujuan akhir, lengkap dengan UTM

            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('human_clicks')->default(0);
            $table->timestamp('last_clicked_at')->nullable();

            $table->boolean('active')->default(true)->index();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('short_links');
    }
};

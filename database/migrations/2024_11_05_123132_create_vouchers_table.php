<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama voucher
            $table->string('slug')->unique(); // Slug untuk URL
            $table->string('image_url'); // URL gambar utama
            $table->string('data_src'); // URL gambar data-src untuk lazy loading
            $table->string('voucher_url'); // URL voucher
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};

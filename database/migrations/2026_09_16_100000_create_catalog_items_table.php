<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode grup dari Saweria, mis. DG-MOBILELEGENDSBANGBANG
            $table->unsignedBigInteger('external_id')->nullable(); // id grup di Saweria
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('publisher')->nullable();
            $table->string('cover')->nullable(); // URL gambar dari CDN Saweria

            $table->string('type')->index();      // game | product -- hasil klasifikasi kita
            $table->string('variant')->index();   // DIGITAL | VOUCHER | PREGENERATE_VOUCHER -- dari Saweria
            $table->boolean('type_overridden')->default(false); // true kalau jenisnya dari daftar manual

            $table->string('status')->default('ACTIVE');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_items');
    }
};

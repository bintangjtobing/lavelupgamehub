<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            // Urutan tampil di section "Paling Laris". NULL = tidak ditampilkan di sana.
            $table->unsignedSmallInteger('featured_rank')->nullable()->index()->after('status');
        });
    }

    public function down()
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->dropColumn('featured_rank');
        });
    }
};

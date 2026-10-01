<?php

namespace App\Providers;

use App\Services\Analytics\VisitorTracker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Satu pelacak per permintaan. Controller (misalnya halaman produk) dan
        // middleware TrackVisitor harus berbagi sesi yang sama; kalau tidak,
        // kunjungan pertama ke halaman produk tercatat sebagai dua sesi.
        $this->app->scoped(VisitorTracker::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

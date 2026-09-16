<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'message', 'agree_terms'
    ];

    /**
     * Ulasan yang boleh tampil di halaman.
     *
     * Data contoh dari seeder (berdomain @example.com) hanya ikut tampil saat
     * APP_ENV=local, tempat data itu dipakai untuk menata tampilan. Di situs
     * yang diakses publik hanya ulasan sungguhan yang ditampilkan.
     *
     * Penyaringnya ditaruh di model, bukan di masing-masing controller, supaya
     * halaman baru tidak bisa lupa memakainya.
     */
    public function scopePublished($query)
    {
        $query->where('agree_terms', true)->orderBy('created_at', 'DESC');

        if (! app()->environment('local')) {
            $query->where(function ($inner) {
                $inner->whereNull('email')->orWhere('email', 'not like', '%@example.com');
            });
        }

        return $query;
    }
}

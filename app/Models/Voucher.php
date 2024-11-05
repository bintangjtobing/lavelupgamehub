<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image_url',
        'voucher_url',
    ];

    /**
     * Override the boot method to auto-generate slug from the name.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            $voucher->slug = Str::slug($voucher->name);
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    use HasFactory;

    const TYPE_GAME = 'game';
    const TYPE_PRODUCT = 'product';

    // Kategori untuk tab di halaman depan
    const CATEGORY_GAME = 'game';
    const CATEGORY_VOUCHER = 'voucher';
    const CATEGORY_ENTERTAINMENT = 'entertainment';

    protected $fillable = [
        'code',
        'external_id',
        'name',
        'slug',
        'publisher',
        'cover',
        'type',
        'variant',
        'type_overridden',
        'status',
        'featured_rank',
        'synced_at',
    ];

    protected $casts = [
        'type_overridden' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function scopeGames($query)
    {
        return $query->where('type', self::TYPE_GAME);
    }

    public function scopeProducts($query)
    {
        return $query->where('type', self::TYPE_PRODUCT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeBestSellers($query)
    {
        return $query->whereNotNull('featured_rank')->orderBy('featured_rank');
    }

    /*
     * Kategori tab. Tiga-tiganya saling lepas, jadi tiap item hanya masuk satu tab:
     *   game          -> top up langsung ke akun (isi User ID / Zone ID)
     *   voucher       -> game, tapi ditebus lewat kode
     *   entertainment -> bukan game: aplikasi live, streaming, langganan
     */
    public function getCategoryAttribute()
    {
        if ($this->type === self::TYPE_PRODUCT) {
            return self::CATEGORY_ENTERTAINMENT;
        }

        return $this->variant === 'DIGITAL'
            ? self::CATEGORY_GAME
            : self::CATEGORY_VOUCHER;
    }

    public function getCategoryLabelAttribute()
    {
        return [
            self::CATEGORY_GAME => 'Game',
            self::CATEGORY_VOUCHER => 'Voucher',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
        ][$this->category] ?? 'Game';
    }

    // Halaman produk di toko top up Saweria
    public function getTopupUrlAttribute()
    {
        return rtrim(config('saweria.store_url'), '/') . '/' . $this->slug;
    }

    /*
     * Alias di bawah menjaga Blade lama tetap jalan. Template masih memanggil
     * $game->image_url, $game->game_url, $voucher->voucher_url dan seterusnya,
     * jadi field itu dipetakan ke kolom baru daripada mengubah seluruh view.
     */
    public function getImageUrlAttribute()
    {
        $localImage = config('catalog_images.'.$this->slug);

        return $localImage ? asset($localImage) : $this->cover;
    }

    public function getDataSrcAttribute()
    {
        return $this->image_url;
    }

    public function getGameUrlAttribute()
    {
        return route('topup.show', ['slug' => $this->slug]);
    }

    public function getVoucherUrlAttribute()
    {
        return $this->game_url;
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at;
    }
}

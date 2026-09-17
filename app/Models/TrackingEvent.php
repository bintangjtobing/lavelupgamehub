<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    // Langkah funnel, berurutan dari paling awal
    const PAGE_VIEW = 'page_view';
    const PRODUCT_VIEW = 'product_view';
    const CHECKOUT_CLICK = 'checkout_click';
    const ORDER_CREATED = 'order_created';
    const ORDER_PAID = 'order_paid';

    // Peristiwa lain di luar funnel pembelian
    const REVIEW_SUBMIT = 'review_submit';
    const CONTACT_SUBMIT = 'contact_submit';
    const ORDER_TRACKED = 'order_tracked';

    protected $fillable = [
        'session_id',
        'name',
        'path',
        'item_slug',
        'product_slug',
        'value',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(VisitorSession::class, 'session_id');
    }

    public static function funnelSteps(): array
    {
        return [
            self::PAGE_VIEW => 'Kunjungan',
            self::PRODUCT_VIEW => 'Lihat produk',
            self::CHECKOUT_CLICK => 'Menuju checkout',
            self::ORDER_CREATED => 'Pesanan dibuat',
            self::ORDER_PAID => 'Pesanan dibayar',
        ];
    }

    public function getLabelAttribute(): string
    {
        return [
            self::PAGE_VIEW => 'Kunjungan halaman',
            self::PRODUCT_VIEW => 'Lihat produk',
            self::CHECKOUT_CLICK => 'Menuju checkout',
            self::ORDER_CREATED => 'Pesanan dibuat',
            self::ORDER_PAID => 'Pesanan dibayar',
            self::REVIEW_SUBMIT => 'Kirim ulasan',
            self::CONTACT_SUBMIT => 'Kirim pesan',
            self::ORDER_TRACKED => 'Cek status pesanan',
        ][$this->name] ?? $this->name;
    }
}

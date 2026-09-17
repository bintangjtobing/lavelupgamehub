<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'saweria_id',
        'session_id',
        'attribution',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'donator_name',
        'donator_email',
        'message',
        'amount_raw',
        'game_name',
        'product_name',
        'cover',
        'payment_method',
        'state',
        'payment_status',
        'fulfillment_status',
        'product_price',
        'fee',
        'currency',
        'ordered_at',
        'paid_at',
        'enriched_at',
        'enrich_attempts',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'ordered_at' => 'datetime',
        'paid_at' => 'datetime',
        'enriched_at' => 'datetime',
    ];

    /**
     * Pesanan yang detail produknya belum berhasil diambil dari Saweria.
     */
    public function scopeNeedsEnrichment($query)
    {
        return $query->whereNull('enriched_at')->where('enrich_attempts', '<', 10);
    }

    /**
     * Pesanan yang statusnya masih bisa berubah, jadi perlu diperiksa berkala.
     */
    public function session()
    {
        return $this->belongsTo(VisitorSession::class, 'session_id');
    }

    /**
     * Label sumber trafik pesanan. Kosong berarti pesanan tidak bisa
     * dihubungkan dengan kunjungan mana pun, bukan berarti datang langsung.
     */
    public function getSourceLabelAttribute(): string
    {
        if ($this->utm_source) {
            return $this->utm_medium
                ? $this->utm_source.' / '.$this->utm_medium
                : $this->utm_source;
        }

        return $this->session_id ? 'langsung' : 'tidak terlacak';
    }

    public function getStateLabelAttribute(): string
    {
        return [
            'completed' => 'Selesai',
            'processing' => 'Diproses',
            'pending' => 'Menunggu bayar',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'refunded' => 'Dikembalikan',
        ][$this->state] ?? 'Belum diketahui';
    }

    public function scopeUnsettled($query)
    {
        return $query->whereIn('state', ['pending', 'processing', 'unknown']);
    }
}

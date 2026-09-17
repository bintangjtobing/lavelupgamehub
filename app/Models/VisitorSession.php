<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorSession extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'referrer_host',
        'landing_path',
        'device',
        'is_bot',
        'bot_name',
        'page_views',
        'product_views',
        'checkout_clicks',
        'started_at',
        'last_seen_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Kunjungan manusia. Dipakai seluruh laporan agar bot tidak
     * mengaburkan angka konversi.
     */
    public function scopeHumans($query)
    {
        return $query->where('is_bot', false);
    }

    public function events()
    {
        return $this->hasMany(TrackingEvent::class, 'session_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'session_id');
    }

    /**
     * Label sumber trafik yang bisa dibaca manusia.
     * Kunjungan tanpa UTM dan tanpa referrer dianggap datang langsung.
     */
    public function getSourceLabelAttribute(): string
    {
        if ($this->utm_source) {
            return $this->utm_medium
                ? $this->utm_source.' / '.$this->utm_medium
                : $this->utm_source;
        }

        return $this->referrer_host ?: 'langsung';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ShortLink extends Model
{
    protected $fillable = [
        'code', 'label', 'template', 'target',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'active', 'expires_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
        'last_clicked_at' => 'datetime',
    ];

    /**
     * Huruf yang mudah dibedakan saat dibaca atau diketik ulang.
     * Angka 0/1 serta huruf O/I/l sengaja dihilangkan.
     */
    protected const ALPHABET = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    public static function generateCode(int $length = 7): string
    {
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isUsable(): bool
    {
        return $this->active && ! $this->hasExpired();
    }

    public function hasExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getShortUrlAttribute(): string
    {
        return url('/s/'.$this->code);
    }

    /**
     * Tujuan akhir beserta UTM yang tertempel.
     *
     * UTM yang sudah ada pada tujuan tidak ditimpa, supaya tautan yang memang
     * sengaja dibuat lengkap oleh pengelola tetap utuh.
     */
    public function getResolvedTargetAttribute(): string
    {
        $utm = array_filter([
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
            'utm_term' => $this->utm_term,
        ]);

        if ($utm === []) {
            return $this->target;
        }

        $parts = parse_url($this->target);
        parse_str($parts['query'] ?? '', $existing);

        $query = array_merge($utm, $existing);

        $url = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '')
            .($parts['port'] ?? null ? ':'.$parts['port'] : '')
            .($parts['path'] ?? '');

        $url .= '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        if (! empty($parts['fragment'])) {
            $url .= '#'.$parts['fragment'];
        }

        return $url;
    }

    public function getTemplateLabelAttribute(): string
    {
        return config('shortlinks.templates.'.$this->template.'.label', 'Bebas');
    }

    public function getTemplateIconAttribute(): string
    {
        return config('shortlinks.templates.'.$this->template.'.icon', '🔗');
    }

    public function registerClick(bool $isBot): void
    {
        $this->increment('clicks');

        if (! $isBot) {
            $this->increment('human_clicks');
        }

        $this->forceFill(['last_clicked_at' => Carbon::now()])->saveQuietly();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function slugSuggestion(string $label): string
    {
        return Str::limit(Str::slug($label), 30, '');
    }
}

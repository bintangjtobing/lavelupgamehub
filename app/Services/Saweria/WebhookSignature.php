<?php

namespace App\Services\Saweria;

/*
 * Memverifikasi header Saweria-Callback-Signature.
 *
 * Tanpa pemeriksaan ini, siapa pun yang menebak alamat endpoint bisa mengirim
 * pesanan karangan ke basis data kita. Tanda tangan dihitung ulang dari isi
 * callback memakai stream key, lalu dibandingkan dengan yang dikirim Saweria.
 *
 * Rumusnya mengikuti dokumentasi webhook Saweria: lima nilai berikut
 * digabungkan tanpa pemisah, dan URUTANNYA menentukan hasil.
 */
class WebhookSignature
{
    protected const FIELDS = ['version', 'id', 'amount_raw', 'donator_name', 'donator_email'];

    public function __construct(protected ?string $streamKey = null)
    {
        $this->streamKey = $streamKey ?? config('saweria.stream_key');
    }

    public function isConfigured(): bool
    {
        return is_string($this->streamKey) && trim($this->streamKey) !== '';
    }

    /**
     * Membandingkan tanda tangan secara constant-time supaya tidak bisa ditebak
     * lewat selisih waktu perbandingan.
     */
    public function verify(array $payload, ?string $provided): bool
    {
        if (! $this->isConfigured() || ! is_string($provided) || $provided === '') {
            return false;
        }

        $expected = $this->expected($payload);

        return hash_equals($expected, strtolower(trim($provided)));
    }

    public function expected(array $payload): string
    {
        $message = '';

        foreach (self::FIELDS as $field) {
            $message .= $this->stringify($payload[$field] ?? null);
        }

        return hash_hmac('sha256', $message, (string) $this->streamKey);
    }

    /**
     * Angka pada callback dikirim sebagai bilangan JSON, jadi harus diubah ke
     * teks dengan bentuk yang sama seperti saat Saweria menghitung tanda tangan.
     */
    protected function stringify(mixed $value): string
    {
        if ($value === null || is_bool($value)) {
            return '';
        }

        if (is_float($value) && floor($value) === $value && abs($value) < PHP_INT_MAX) {
            return (string) (int) $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }
}

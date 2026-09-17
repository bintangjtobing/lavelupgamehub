<?php

namespace App\Services\Google;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/*
 * Menukar kunci service account menjadi access token OAuth2.
 *
 * Ditulis langsung memakai JWT + HTTP biasa, tanpa google/apiclient. Pustaka
 * resmi itu menarik puluhan dependensi dan gRPC, sementara yang dibutuhkan di
 * sini hanya dua permintaan REST. Penandatanganan RS256 sudah tersedia di
 * ekstensi openssl bawaan PHP.
 */
class ServiceAccountToken
{
    protected const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    protected const SCOPES = [
        'https://www.googleapis.com/auth/analytics.readonly',
        'https://www.googleapis.com/auth/webmasters.readonly',
    ];

    public function isConfigured(): bool
    {
        $path = config('google.credentials');

        return is_string($path) && $path !== '' && is_readable($path);
    }

    /**
     * Email service account, berguna untuk ditampilkan pada panduan
     * penyiapan agar pengelola tahu akun mana yang harus diberi akses.
     */
    public function clientEmail(): ?string
    {
        try {
            return $this->credentials()['client_email'] ?? null;
        } catch (RuntimeException) {
            return null;
        }
    }

    public function accessToken(): string
    {
        // Google memberi token berumur satu jam; disimpan sedikit lebih pendek.
        return Cache::remember('google.access_token', 3300, function () {
            $credentials = $this->credentials();
            $assertion = $this->assertion($credentials);

            $response = Http::asForm()->timeout(15)->post(self::TOKEN_URL, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'Google menolak kunci service account (HTTP '.$response->status().'). '
                    .'Periksa kembali berkas kredensial dan jam server.'
                );
            }

            $token = $response->json('access_token');

            if (! is_string($token) || $token === '') {
                throw new RuntimeException('Google tidak mengembalikan access token.');
            }

            return $token;
        });
    }

    protected function credentials(): array
    {
        $path = config('google.credentials');

        if (! is_string($path) || $path === '' || ! is_readable($path)) {
            throw new RuntimeException('Berkas kredensial Google belum tersedia.');
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            throw new RuntimeException('Berkas kredensial Google tidak berisi service account yang sah.');
        }

        return $data;
    }

    protected function assertion(array $credentials): string
    {
        $now = time();

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $credentials['client_email'],
            'scope' => implode(' ', self::SCOPES),
            'aud' => self::TOKEN_URL,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $payload = $this->base64url(json_encode($header)).'.'.$this->base64url(json_encode($claims));

        $signature = '';
        $key = openssl_pkey_get_private($credentials['private_key']);

        if ($key === false || ! openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Gagal menandatangani permintaan token Google.');
        }

        return $payload.'.'.$this->base64url($signature);
    }

    protected function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}

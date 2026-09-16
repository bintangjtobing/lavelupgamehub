{{--
    Avatar inisial berbasis CSS.

    Sebelumnya tiap avatar dibuat sebagai gambar base64 lewat Avatar::create().
    Dengan 120 ulasan, gambar-gambar itu menambah sekitar 314 KB ke HTML --
    hampir separuh berat halaman. Versi ini nol byte tambahan.

    Variabel: $name, $size (opsional: 'sm')
--}}
@php
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [$name];
    $initials = mb_strtoupper(mb_substr($parts[0], 0, 1));
    if (count($parts) > 1) {
        $initials .= mb_strtoupper(mb_substr(end($parts), 0, 1));
    }

    // Warna diturunkan dari nama, jadi orang yang sama selalu dapat warna sama
    $hue = crc32($name) % 360;
@endphp

<span class="lu-avatar {{ ($size ?? '') === 'sm' ? 'lu-avatar-sm' : '' }}"
    style="--lu-avatar-hue: {{ $hue }}" aria-hidden="true">{{ $initials }}</span>

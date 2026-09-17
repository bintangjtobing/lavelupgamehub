@extends('admin.layout')
@section('title', 'Tautan Pendek')

@php
    $n = fn ($v) => number_format((int) $v, 0, ',', '.');
@endphp

@section('content')
    <div class="lup-head">
        <div>
            <h1>Tautan Pendek</h1>
            <p>
                {{ $links->total() }} tautan &middot; {{ $n($humanClicks) }} klik orang
                dari {{ $n($totalClicks) }} total
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="lup-error" style="margin-bottom:18px">{{ $errors->first() }}</div>
    @endif

    {{-- Pembuat tautan --}}
    <div class="lup-panel" style="margin-bottom:18px">
        <h2>Buat tautan baru</h2>
        <p class="hint">
            Pilih template, lalu ubah nilainya sesuka hati. Template hanya mengisi nilai awal
            supaya penamaan UTM konsisten &mdash; menulis "ig" hari ini dan "instagram" besok
            akan terbaca sebagai dua sumber berbeda di laporan.
        </p>

        <div class="lup-chips" id="tpl-chips" style="margin-bottom:16px">
            @foreach ($templates as $key => $tpl)
                <a href="#" data-tpl="{{ $key }}"
                    data-values="{{ json_encode(Illuminate\Support\Arr::only($tpl, ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'])) }}"
                    title="{{ $tpl['description'] ?? '' }}">{{ $tpl['icon'] }} {{ $tpl['label'] }}</a>
            @endforeach
        </div>

        <p class="hint" id="tpl-desc" style="min-height:18px">Pilih salah satu template di atas.</p>

        <form method="post" action="{{ route('admin.shortlinks.store') }}" id="tpl-form">
            @csrf
            <input type="hidden" name="template" id="f-template" value="custom">

            <div class="lup-form-grid">
                <label>Nama tautan
                    <input type="text" name="label" required placeholder="Promo ML Oktober"
                        value="{{ old('label') }}">
                </label>

                <label>Tujuan
                    <input type="url" name="target" required
                        placeholder="https://levelupgamehub.com/topup/mobile-legends-bang-bang"
                        value="{{ old('target', url('/')) }}">
                </label>

                <label>Kode <span class="lup-muted">(kosongkan = dibuatkan acak)</span>
                    <input type="text" name="code" placeholder="promo-ml" value="{{ old('code') }}"
                        pattern="[A-Za-z0-9\-]+">
                </label>

                <label>Berlaku sampai <span class="lup-muted">(opsional)</span>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}">
                </label>

                <label>utm_source
                    <input type="text" name="utm_source" id="f-utm_source" value="{{ old('utm_source') }}">
                </label>

                <label>utm_medium
                    <input type="text" name="utm_medium" id="f-utm_medium" value="{{ old('utm_medium') }}">
                </label>

                <label>utm_campaign
                    <input type="text" name="utm_campaign" id="f-utm_campaign" value="{{ old('utm_campaign') }}">
                </label>

                <label>utm_content
                    <input type="text" name="utm_content" id="f-utm_content" value="{{ old('utm_content') }}">
                </label>

                <label>utm_term
                    <input type="text" name="utm_term" id="f-utm_term" value="{{ old('utm_term') }}">
                </label>
            </div>

            <button type="submit" class="lup-btn" style="margin-top:14px">Buat Tautan</button>
        </form>
    </div>

    {{-- Daftar tautan --}}
    @if ($links->isEmpty())
        <div class="lup-empty">Belum ada tautan pendek. Buat yang pertama lewat formulir di atas.</div>
    @else
        <div class="lup-panel">
            <h2>Tautan aktif</h2>
            <p class="hint">
                Klik orang sudah dipisahkan dari bot, jadi pratinjau tautan di WhatsApp
                atau Facebook tidak ikut terhitung.
            </p>

            <div class="lup-scroll">
                <table class="lup-table">
                    <thead>
                        <tr>
                            <th>Tautan</th>
                            <th>Sumber</th>
                            <th>Tujuan</th>
                            <th class="num">Klik orang</th>
                            <th class="num">Total</th>
                            <th>Terakhir</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($links as $link)
                            <tr>
                                <td>
                                    <strong>{{ $link->template_icon }} {{ $link->label }}</strong>
                                    <div class="lup-mono">
                                        <a href="{{ $link->short_url }}" target="_blank" rel="noopener">/s/{{ $link->code }}</a>
                                    </div>
                                    @unless ($link->isUsable())
                                        <span class="lup-badge failed">
                                            {{ $link->hasExpired() ? 'kedaluwarsa' : 'nonaktif' }}
                                        </span>
                                    @endunless
                                </td>
                                <td class="lup-muted">
                                    {{ $link->utm_source ?: '-' }}
                                    @if ($link->utm_medium)
                                        / {{ $link->utm_medium }}
                                    @endif
                                    @if ($link->utm_campaign)
                                        <div style="font-size:11px">{{ $link->utm_campaign }}</div>
                                    @endif
                                </td>
                                <td class="lup-muted lup-mono" style="max-width:260px; word-break:break-all">
                                    {{ Illuminate\Support\Str::limit($link->target, 60) }}
                                </td>
                                <td class="num"><strong>{{ $n($link->human_clicks) }}</strong></td>
                                <td class="num lup-muted">{{ $n($link->clicks) }}</td>
                                <td class="lup-muted">
                                    {{ $link->last_clicked_at?->translatedFormat('d M, H:i') ?: 'belum' }}
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px">
                                        <form method="post" action="{{ route('admin.shortlinks.toggle', $link) }}">
                                            @csrf
                                            <button type="submit" class="lup-btn ghost" style="padding:5px 10px; font-size:12px">
                                                {{ $link->active ? 'Matikan' : 'Nyalakan' }}
                                            </button>
                                        </form>
                                        <form method="post" action="{{ route('admin.shortlinks.destroy', $link) }}"
                                            onsubmit="return confirm('Hapus /s/{{ $link->code }}? Tautan yang sudah tersebar tidak akan berfungsi lagi.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="lup-btn ghost" style="padding:5px 10px; font-size:12px">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lup-pag">{{ $links->links('admin.pagination') }}</div>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const chips = document.querySelectorAll('#tpl-chips a');
            const desc = document.getElementById('tpl-desc');
            const hidden = document.getElementById('f-template');

            chips.forEach(chip => {
                chip.addEventListener('click', function (e) {
                    e.preventDefault();

                    chips.forEach(c => c.classList.remove('is-on'));
                    this.classList.add('is-on');

                    hidden.value = this.dataset.tpl;
                    desc.textContent = this.getAttribute('title') || '';

                    // Template hanya mengisi nilai awal; semuanya masih bisa diubah.
                    const values = JSON.parse(this.dataset.values || '{}');
                    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term']
                        .forEach(key => {
                            const field = document.getElementById('f-' + key);
                            if (field) field.value = values[key] || '';
                        });
                });
            });
        })();
    </script>
@endpush

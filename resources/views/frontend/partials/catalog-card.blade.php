{{--
    Kartu katalog. Dipakai ulang di section "Paling Laris" dan di penjelajah katalog.
    Variabel: $item (CatalogItem), $badge (opsional, teks label di pojok gambar)
--}}
<a href="{{ $item->game_url }}" title="Lihat pilihan top up {{ $item->name }}"
    data-analytics-item="{{ json_encode(['item_id' => $item->slug, 'item_name' => $item->name, 'item_brand' => $item->publisher, 'item_category' => $item->category_label]) }}"
    class="lu-card"
    data-category="{{ $item->category }}"
    data-name="{{ \Illuminate\Support\Str::lower($item->name) }}"
    @isset($isNew) data-new="1" @endisset
    @isset($hidden) hidden @endisset>
    <div class="lu-card-media">
        <img src="{{ $item->image_url }}" alt="Cover {{ $item->name }}" title="{{ $item->name }} di LevelUp Market" loading="lazy" decoding="async" width="300"
            height="300">
        @isset($badge)
            <span class="lu-card-badge">{{ $badge }}</span>
        @endisset
    </div>
    <div class="lu-card-body">
        <h3 class="lu-card-name">{{ $item->name }}</h3>
        <p class="lu-card-publisher">{{ $item->publisher ?: $item->category_label }}</p>
    </div>
</a>

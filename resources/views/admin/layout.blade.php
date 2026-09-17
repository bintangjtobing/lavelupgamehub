<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Panel') &middot; LevelUp Market</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/admin.css') }}">
</head>

<body>
    <header class="lup-top">
        <div class="lup-wrap lup-top-inner">
            <a href="{{ route('admin.dashboard') }}" class="lup-brand">LevelUp <span>Panel</span></a>

            <nav class="lup-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'is-on' : '' }}">Analitik</a>
                <a href="{{ route('admin.insights') }}"
                    class="{{ request()->routeIs('admin.insights') ? 'is-on' : '' }}">Google Insight</a>
                <a href="{{ route('admin.shortlinks') }}"
                    class="{{ request()->routeIs('admin.shortlinks*') ? 'is-on' : '' }}">Tautan</a>
                <a href="{{ route('admin.orders') }}"
                    class="{{ request()->routeIs('admin.orders*') ? 'is-on' : '' }}">Pesanan</a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener">Lihat situs &rarr;</a>
            </nav>

            <form method="post" action="{{ route('admin.logout') }}" class="lup-logout">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </header>

    <main class="lup-wrap lup-main">
        @if (session('status'))
            <div class="lup-flash">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>

</html>

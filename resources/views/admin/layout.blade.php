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
    <header class="ad-top">
        <div class="ad-wrap ad-top-inner">
            <a href="{{ route('admin.dashboard') }}" class="ad-brand">LevelUp <span>Panel</span></a>

            <nav class="ad-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'is-on' : '' }}">Analitik</a>
                <a href="{{ route('admin.orders') }}"
                    class="{{ request()->routeIs('admin.orders*') ? 'is-on' : '' }}">Pesanan</a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener">Lihat situs &rarr;</a>
            </nav>

            <form method="post" action="{{ route('admin.logout') }}" class="ad-logout">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </header>

    <main class="ad-wrap ad-main">
        @if (session('status'))
            <div class="ad-flash">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>

</html>

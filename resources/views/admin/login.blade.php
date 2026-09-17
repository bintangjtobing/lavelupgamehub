<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk &middot; LevelUp Panel</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/admin.css') }}">
</head>

<body>
    <div class="ad-login">
        <form method="post" action="{{ route('admin.login') }}">
            @csrf
            <h1>LevelUp Panel</h1>
            <p>Masuk untuk melihat pesanan dan analitik.</p>

            @if ($errors->any())
                <div class="ad-error">{{ $errors->first() }}</div>
            @endif

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username">

            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <button type="submit" class="ad-btn">Masuk</button>
        </form>
    </div>
</body>

</html>

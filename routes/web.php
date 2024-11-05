<?php

use Illuminate\Support\Facades\Route;
use App\Models\Game;
use App\Models\Voucher;

Route::get('/', function () {
    $games = Game::orderBy('created_at', direction: 'DESC')->get();
    return view('pages.index', compact('games'));
});

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/contact', function () {
    return view('pages.contact');
});

Route::get('/topup', function () {
    $games = Game::orderBy('created_at', 'DESC')->paginate(12);
    $vouchers = Voucher::orderBy('created_at', 'DESC')->paginate(12);
    return view('pages.topup', compact('games', 'vouchers'));
});

Route::get('/faq', function () {
    return view('pages.faq');
});
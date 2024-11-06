<?php

use Illuminate\Support\Facades\Route;
use App\Models\Game;
use App\Models\Voucher;
use App\Models\Review;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    $games = Game::orderBy('created_at', direction: 'DESC')->limit('18')->get();
    $reviews = Review::where('agree_terms', true)->orderBy('created_at', 'DESC')->get();
    return view('pages.index', compact('games','reviews'));
});

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/contact', function () {
    return view('pages.contact');
});

Route::get('/topup', function () {
    $games = Game::orderBy('created_at', 'DESC')->paginate(24);
    $vouchers = Voucher::orderBy('created_at', 'DESC')->paginate(24);
    return view('pages.topup', compact('games', 'vouchers'));
});

Route::get('/faq', function () {
    return view('pages.faq');
});

Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/terms-and-conditions', 'pages.terms-condition')->name('terms-and-conditions');
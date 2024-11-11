<?php

use Illuminate\Support\Facades\Route;
use App\Models\Game;
use App\Models\Voucher;
use App\Models\Review;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    $games = Game::orderBy('created_at', direction: 'DESC')->limit('18')->get();
    $reviews = Review::where('agree_terms', true)->orderBy('created_at', 'DESC')->get();
    return view('pages.index', compact('games','reviews'));
});

Route::get('/about', function () {
    $reviews = Review::where('agree_terms', true)->orderBy('created_at', 'DESC')->get();
    return view('pages.about',compact('reviews'));
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

use Illuminate\Support\Facades\Mail;

Route::post('/message', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    $details = [
        'title' => 'Pesan dari Contact Form',
        'name' => $request->name,
        'email' => $request->email,
        'message' => $request->message,
    ];

    Mail::send([], [], function ($message) use ($details) {
        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->to('levelupmarketgaming@gmail.com')
                ->subject($details['title'])
                ->setBody("Nama: {$details['name']}\nEmail: {$details['email']}\nPesan: {$details['message']}", 'text/plain');
    });

    return back()->with('success', 'Pesan berhasil dikirim!');
});


Route::get('/games/search', [GameController::class, 'search'])->name('games.search');

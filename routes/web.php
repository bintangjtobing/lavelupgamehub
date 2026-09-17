<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CheckoutRedirectController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaweriaWebhookController;
use App\Http\Controllers\SeoController;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home']);

Route::get('/about', function () {
    $reviews = Review::published()->get();

    return view('pages.about', compact('reviews'));
});

Route::get('/contact', function () {
    return view('pages.contact');
});

Route::get('/topup', [CatalogController::class, 'topup']);
Route::get('/topup/{slug}', [ProductController::class, 'show'])
    ->middleware('throttle:60,1')->name('topup.show');

Route::get('/track-order', [OrderTrackingController::class, 'index'])
    ->middleware('throttle:60,1')->name('orders.track');
Route::post('/track-order', [OrderTrackingController::class, 'lookup'])
    ->middleware('throttle:order-tracking')->name('orders.lookup');

// Callback pesanan dari Saweria. Dikecualikan dari CSRF di VerifyCsrfToken,
// keasliannya diperiksa lewat tanda tangan HMAC di controller.
Route::post('/webhooks/saweria', SaweriaWebhookController::class)
    ->middleware('throttle:120,1')->name('webhooks.saweria');


// Perantara sebelum berpindah ke pembayaran Saweria; mencatat langkah checkout.
Route::get('/ke-checkout/{slug}', CheckoutRedirectController::class)
    ->middleware('throttle:120,1')->name('checkout.go');

// Panel pengelola
Route::prefix('admin')->name('admin.')->middleware('noindex')->group(function () {
    Route::get('/masuk', [LoginController::class, 'show'])->middleware('guest')->name('login');
    Route::post('/masuk', [LoginController::class, 'login'])->middleware(['guest', 'throttle:10,1']);
    Route::post('/keluar', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/pesanan/{saweriaId}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{saweriaId}/segarkan', [AdminOrderController::class, 'refresh'])->name('orders.refresh');
    });
});

Route::get('/faq', function () {
    $reviews = Review::published()->get();

    return view('pages.faq', compact('reviews'));
});

Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/terms-and-conditions', 'pages.terms-condition')->name('terms-and-conditions');

Route::post('/message', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    $body = "Nama: {$request->name}\nEmail: {$request->email}\n\nPesan:\n{$request->message}";

    // setBody() milik Swift Mailer dan sudah tidak ada sejak Laravel 9 pakai
    // Symfony Mailer, jadi pesan teks polos dikirim lewat Mail::raw()
    Mail::raw($body, function ($message) use ($request) {
        $message->from(config('mail.from.address'), config('mail.from.name'))
            ->to('levelupmarketgaming@gmail.com')
            ->replyTo($request->email, $request->name)
            ->subject('Pesan dari Contact Form');
    });

    return back()
        ->with('success', 'Pesan berhasil dikirim!')
        ->with('analytics_event', 'generate_lead');
});

Route::get('/games/search', [GameController::class, 'search'])->name('games.search');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('seo.llms');

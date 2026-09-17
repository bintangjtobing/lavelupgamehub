<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    /*
     * Cookie milik Google Analytics dipasang oleh skrip gtag di peramban, jadi
     * isinya bukan hasil enkripsi Laravel. Tanpa pengecualian ini, pembacaan
     * dari sisi server selalu gagal mendekripsi dan mengembalikan null.
     */
    protected $except = [
        '_ga',
        '_ga_*',
    ];
}

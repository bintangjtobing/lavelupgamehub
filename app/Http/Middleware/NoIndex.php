<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 * Melarang mesin pencari mengindeks halaman pengelola.
 *
 * Tag <meta robots> saja tidak cukup: robots.txt melarang crawl /admin, dan
 * halaman yang tidak boleh di-crawl juga tidak dibaca isinya -- termasuk meta
 * tag di dalamnya. Header ini terbaca pada tingkat HTTP, jadi tetap berlaku
 * untuk perayap yang mengabaikan robots.txt maupun respons non-HTML.
 */
class NoIndex
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');

        return $response;
    }
}

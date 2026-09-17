<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Satu-satunya area bertanda masuk di situs ini adalah panel pengelola.
        return $request->expectsJson() ? null : route('admin.login');
    }
}

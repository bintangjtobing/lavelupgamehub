<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Callback dari Saweria tidak membawa token CSRF. Keasliannya diperiksa
        // lewat header Saweria-Callback-Signature di SaweriaWebhookController.
        'webhooks/saweria',
    ];
}

<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // protected $addHttpCookie = true;
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     *
     *
     * @var array<int, string>
     *
     */
    protected $except = [
        //
    //   env("FRONTEND_URL ")
    'http://127.0.0.1:3000'
    ];
}

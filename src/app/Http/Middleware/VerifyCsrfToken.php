<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
           'authService/*', 'testService/*','gidService/*','fileUpload/*', 'lineService/*',
           'chat/*',  'productService/*', 'coreService/*', 'fileService/*', 'commonService/*', 'healthCheckService/*',
           'vehicleCheckService/*',
    ];
}

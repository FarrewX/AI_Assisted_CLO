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
        // ถ้ามี route ที่อยากยกเว้น เช่น API หรือ webhook ก็เพิ่มตรงนี้
        // '/webhook/*'
    ];
}

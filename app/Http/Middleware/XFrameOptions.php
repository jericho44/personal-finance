<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XFrameOptions
{
    /**
     * Handle an incoming HTTP request.
     *
     * Middleware ini digunakan untuk mencegah serangan Clickjacking dengan
     * membatasi siapa saja yang dapat melakukan embedding halaman ini ke dalam
     * elemen <iframe>, <object>, atau <embed>.
     *
     * Secara default, middleware ini menambahkan dua header keamanan:
     *
     * 1. X-Frame-Options
     *    - SAMEORIGIN → Hanya halaman dari domain yang sama yang dapat melakukan embed.
     *    - DENY → Tidak ada domain yang boleh melakukan embed.
     *    - ALLOW-FROM https://example.com → (Deprecated, tidak disarankan)
     *
     * 2. Content-Security-Policy
     *    - frame-ancestors 'self' → Standar modern pengganti X-Frame-Options.
     *      Anda bisa menambahkan domain lain, misal:
     *        frame-ancestors 'self' https://partner.example.com
     *
     * Contoh penggunaan:
     * -------------------------------------------------
     * // Daftarkan di app/Http/Kernel.php:
     * protected $middleware = [
     *     // ...
     *     \App\Http\Middleware\XFrameOptions::class,
     * ];
     *
     * // Atau untuk route tertentu:
     * Route::get('/dashboard', [DashboardController::class, 'index'])
     *     ->middleware('xframe');
     * -------------------------------------------------
     *
     * @param  \Illuminate\Http\Request  $request  HTTP request yang diterima.
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next  Callback untuk meneruskan request.
     * @return \Symfony\Component\HttpFoundation\Response Response yang sudah disisipi header keamanan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Batasi iframe agar hanya bisa diakses dari domain yang sama
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Standar modern pengganti X-Frame-Options
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

        return $response;
    }
}

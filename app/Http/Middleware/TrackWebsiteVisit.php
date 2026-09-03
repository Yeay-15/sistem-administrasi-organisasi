<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisit
{
    /**
     * Mencatat kunjungan ke halaman publik secara ringan (path, nama route,
     * referrer, dan "visitor_key" ter-hash) — dipakai untuk menyusun
     * Statistik Website di Dashboard admin. Dijalankan setelah response
     * terbentuk supaya tidak menambah latensi pada request pengunjung, dan
     * dibungkus try/catch agar kegagalan pencatatan tidak pernah mengganggu
     * pengalaman pengunjung situs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('get') && ! $this->looksLikeBot($request)) {
            try {
                PageVisit::create([
                    'path' => '/' . ltrim($request->path(), '/'),
                    'route_name' => $request->route()?->getName(),
                    'visitor_key' => hash('sha256', $request->ip() . '|' . $request->userAgent() . '|' . now()->toDateString()),
                    'referrer' => $request->headers->get('referer'),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Diamkan — pelacakan kunjungan tidak boleh mengganggu situs.
            }
        }

        return $response;
    }

    private function looksLikeBot(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());

        if ($userAgent === '') {
            return true;
        }

        foreach (['bot', 'spider', 'crawl', 'slurp', 'facebookexternalhit', 'preview', 'monitor'] as $needle) {
            if (str_contains($userAgent, $needle)) {
                return true;
            }
        }

        return false;
    }
}

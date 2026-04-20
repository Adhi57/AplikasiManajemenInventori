<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiKeyMiddleware
{
    /**
     * Batas maksimal percobaan gagal sebelum IP diblokir sementara.
     */
    private const MAX_ATTEMPTS    = 10;
    private const DECAY_SECONDS   = 60;   // blokir selama 60 detik

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // ── 1. Rate Limit: blokir jika terlalu banyak percobaan gagal ──────────
        $rateLimitKey = "api_fail:{$ip}";
        $attempts     = Cache::get($rateLimitKey, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Log::warning("API: IP {$ip} diblokir sementara karena terlalu banyak percobaan gagal.");
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan gagal. Coba lagi dalam 1 menit.',
            ], 429);
        }

        // ── 2. Ambil API key HANYA dari header (JANGAN dari query string) ──────
        $apiKey = $request->header('X-API-KEY');

        // Tolak jika key dikirim via query string (tidak aman)
        if (!$apiKey && $request->has('api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'API key harus dikirim via header X-API-KEY, bukan query string.',
                'hint'    => 'Tambahkan header: X-API-KEY: <your-key>',
            ], 401);
        }

        $validKey = config('app.api_key');

        // ── 3. Validasi dengan hash_equals (anti timing attack) ────────────────
        $isValid = $apiKey && $validKey && hash_equals($validKey, $apiKey);

        if (!$isValid) {
            // Catat percobaan gagal
            Cache::put($rateLimitKey, $attempts + 1, self::DECAY_SECONDS);

            Log::warning("API: Percobaan dengan key tidak valid dari IP {$ip}. " .
                         "Percobaan ke-" . ($attempts + 1) . "/" . self::MAX_ATTEMPTS);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. API key tidak valid.',
            ], 401);
        }

        // ── 4. Reset counter jika berhasil ─────────────────────────────────────
        Cache::forget($rateLimitKey);

        return $next($request);
    }
}

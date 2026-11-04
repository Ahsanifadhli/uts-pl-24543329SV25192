<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /**
     * Memeriksa kesehatan aplikasi: koneksi basis data dan cache.
     * Membalas 200 bila sehat, 503 bila ada komponen yang bermasalah,
     * disertai bagian mana yang gagal.
     */
    public function check(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // --- Periksa basis data ---
        try {
            DB::connection()->getPdo();
            DB::select('select 1');
            $checks['database'] = 'ok';
        } catch (\Throwable $e) {
            $healthy = false;
            $checks['database'] = 'gagal';

            // Log terstruktur (error): komponen, aksi, dan sebab.
            // Pesan koneksi tidak memuat data pribadi.
            Log::error('Health check: basis data tidak dapat dihubungi', [
                'komponen' => 'database',
                'aksi' => 'memeriksa koneksi basis data',
                'sebab' => $e->getMessage(),
            ]);
        }

        // --- Periksa cache ---
        try {
            Cache::put('health_check_probe', 'ok', 10);
            $value = Cache::get('health_check_probe');
            $checks['cache'] = $value === 'ok' ? 'ok' : 'gagal';

            if ($checks['cache'] !== 'ok') {
                $healthy = false;
                Log::error('Health check: cache tidak berfungsi sebagaimana mestinya', [
                    'komponen' => 'cache',
                    'aksi' => 'memeriksa baca-tulis cache',
                    'sebab' => 'nilai yang dibaca tidak sama dengan yang ditulis',
                ]);
            }
        } catch (\Throwable $e) {
            $healthy = false;
            $checks['cache'] = 'gagal';

            Log::error('Health check: cache tidak dapat diakses', [
                'komponen' => 'cache',
                'aksi' => 'memeriksa baca-tulis cache',
                'sebab' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Otorisasi
{
    /**
     * Checks whether the authenticated user's Sanctum token has the required
     * ability (or is_admin), mirroring the legacy permission flow.
     */
    public function handle(Request $request, Closure $next, ...$actions)
    {
        $user = $request->user();

        if ($user && $user->tokenCan('is_admin')) {
            return $next($request);
        }

        foreach ($actions as $action) {
            if ($user && $user->tokenCan($action)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Perintah tidak dapat dijalankan!',
            'data' => null,
            'meta' => null,
            'errors' => null,
        ], 401);
    }
}

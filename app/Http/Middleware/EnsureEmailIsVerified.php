<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado y tiene email verificado
        if (!$request->user() || !$request->user()->email_verified_at) {
            return response()->json([
                'error' => 'Email not verified',
                'message' => 'You need to verify your email before accessing this resource.'
            ], 403);
        }

        return $next($request);
    }
}

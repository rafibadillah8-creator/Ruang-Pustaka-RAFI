<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictNgrokTunnelAccess
{
    private const NGROK_SUFFIXES = [
        '.ngrok-free.dev',
        '.ngrok-free.app',
        '.ngrok.app',
        '.ngrok.io',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $usesNgrokHost = false;

        foreach (self::NGROK_SUFFIXES as $suffix) {
            if (str_ends_with($host, $suffix)) {
                $usesNgrokHost = true;
                break;
            }
        }

        if ($usesNgrokHost && (
            !$request->isMethod('post')
            || !$request->is('api/midtrans/notification')
        )) {
            abort(404);
        }

        return $next($request);
    }
}

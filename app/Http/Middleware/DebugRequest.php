<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldLog($request)) {
            Log::debug('debug.request.before', [
                'method' => $request->getMethod(),
                'path' => $request->path(),
                'expects_json' => $request->expectsJson(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax(),
                'accept' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type'),
                'x_xsrf_token' => $request->header('X-XSRF-TOKEN'),
                'session_id' => $request->session()?->getId(),
                'session_token' => $request->session()?->token(),
                'cookies' => $request->cookies->all(),
            ]);
        }

        $response = $next($request);

        if ($this->shouldLog($request)) {
            Log::debug('debug.request.after', [
                'method' => $request->getMethod(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'set_cookies' => $response->headers->get('Set-Cookie', null, false),
            ]);
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        if (! app()->environment('local')) {
            return false;
        }

        return $request->is('login') ||
            $request->is('register') ||
            $request->is('logout') ||
            $request->is('sanctum/csrf-cookie') ||
            $request->is('api/user');
    }
}

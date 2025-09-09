<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $ttl = 300): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Don't cache authenticated requests
        if (\Illuminate\Support\Facades\Auth::check()) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = 'response_' . md5($request->fullUrl());

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            $response = response(Cache::get($cacheKey));
            $response->headers->set('X-Cache', 'HIT');
            return $response;
        }

        // Get response
        $response = $next($request);

        // Cache successful responses
        if ($response->getStatusCode() === 200) {
            Cache::put($cacheKey, $response->getContent(), $ttl);
            $response->headers->set('X-Cache', 'MISS');
        }

        return $response;
    }
}

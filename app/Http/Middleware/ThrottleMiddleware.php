<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottleMiddleware
{
    /**
     * Maximum number of requests allowed within the time window.
     */
    private int $maxRequests;

    /**
     * Time window in minutes.
     */
    private int $decayMinutes;

    public function __construct(int $maxRequests = 60, int $decayMinutes = 1)
    {
        $this->maxRequests  = $maxRequests;
        $this->decayMinutes = $decayMinutes;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxRequests = 60, int $decayMinutes = 1): Response
    {
        // Allow per-route override via middleware parameters
        $this->maxRequests  = $maxRequests;
        $this->decayMinutes = $decayMinutes;

        $key = $this->resolveRequestKey($request);

        if ($this->isRateLimitExceeded($key)) {
            return $this->buildRateLimitResponse($key);
        }

        $this->incrementRequestCount($key);

        $response = $next($request);

        return $this->addRateLimitHeaders($response, $key);
    }

    /**
     * Build a unique cache key per user/IP + route.
     */
    private function resolveRequestKey(Request $request): string
    {
        $identifier = $request->user()?->id ?? $request->ip();
        $route      = $request->route()?->getName() ?? $request->path();

        return 'throttle:' . sha1("{$identifier}|{$route}");
    }

    /**
     * Check if the rate limit has been exceeded.
     */
    private function isRateLimitExceeded(string $key): bool
    {
        return $this->getRequestCount($key) >= $this->maxRequests;
    }

    /**
     * Get current request count from cache.
     */
    private function getRequestCount(string $key): int
    {
        return (int) Cache::get($key, 0);
    }

    /**
     * Increment the request counter, setting expiry on first hit.
     */
    private function incrementRequestCount(string $key): void
    {
        if (! Cache::has($key)) {
            Cache::put($key, 1, now()->addMinutes($this->decayMinutes));
        } else {
            Cache::increment($key);
        }
    }

    /**
     * Get remaining TTL (seconds) for the cache key.
     */
    private function getRemainingTtl(string $key): int
    {
        // TTL isn't directly available in all drivers; compute from decay window.
        return $this->decayMinutes * 60;
    }

    /**
     * Build the 429 Too Many Requests response.
     */
    private function buildRateLimitResponse(string $key): Response
    {
        return response()->json([
            'message'     => 'Too Many Requests. Please slow down.',
            'retry_after' => $this->getRemainingTtl($key),
        ], Response::HTTP_TOO_MANY_REQUESTS)
            ->header('Retry-After', $this->getRemainingTtl($key))
            ->header('X-RateLimit-Limit', $this->maxRequests)
            ->header('X-RateLimit-Remaining', 0);
    }

    /**
     * Attach rate limit headers to the outgoing response.
     */
    private function addRateLimitHeaders(Response $response, string $key): Response
    {
        $remaining = max(0, $this->maxRequests - $this->getRequestCount($key));

        $response->headers->add([
            'X-RateLimit-Limit'     => $this->maxRequests,
            'X-RateLimit-Remaining' => $remaining,
        ]);

        return $response;
    }
}
